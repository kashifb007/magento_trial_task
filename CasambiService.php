<?php

namespace App\Services;

use App\Entity\Area;
use App\Entity\Connection;
use App\Entity\ConnectionEndpoint;
use App\Entity\Device;
use App\Entity\DeviceType;
use App\Helpers\CasambiEndpoints;
use App\Helpers\RequestHelper;
use App\Services\Interfaces\IConnection;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Utils\Crud;
use Utils\ViewModel;

/**
 * Class CasambiService
 *
 * Interacts with Casambi API
 *
 * @package Services
 * @author Kashif
 */
class CasambiService implements IConnection
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var NotifierInterface
     */
    private $notifier;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CasambiService constructor.
     * @param EntityManagerInterface $em
     * @param NotifierInterface $notifier
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em, NotifierInterface $notifier, LoggerInterface $logger)
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl
        ]);

        $this->em = $em;
        $this->notifier = $notifier;
        $this->logger = $logger;
    }

    /**
     * Authenticates the user and stores the established session
     *
     * @param ViewModel $vm
     * @return bool
     * @throws GuzzleException
     */
    public function createConnection(ViewModel $vm):?int
    {
        try {
            $data = $vm->toArray(['api']);

            $data['X-Casambi-Key'] = $data['apiKey'];

            //Get the first endpoint
            $endpoint = $this->em->getRepository(ConnectionEndpoint::class)->findOneBy(['name' => CasambiEndpoints::NETWORK_SESSION]);

            $requestParams = RequestHelper::buildRequest($data, $endpoint);
            $response = $this->client->request($endpoint->getMethod(), $requestParams['uri'], $requestParams['options']);

            $networks = json_decode($response->getBody(), true);

            foreach ($networks as $network) {
                $networks[$network['id']]['X-Casambi-Key'] = $data['X-Casambi-Key'];
            }

            $connection = (new Connection())
                ->setName($vm->getEmail())
                ->setNetworks($networks);
            $this->em->persist($connection);
            $this->em->flush();

            return $connection->getId();

        } catch (ClientException $e) {
            $this->logger->error($e->getTraceAsString());
            $this->notifier->send(new Notification($e->getCode() === Response::HTTP_UNAUTHORIZED ? 'Please check the details that you have entered' : 'Sorry, we\'ve not been able to authenticate you with Casambi',['browser']));
        }
        return false;
    }

    /**
     * Gets a list of devices from API and maps them
     *
     * @param int $connectionId
     * @return bool
     */
    public function importDeviceList(int $connectionId): bool
    {
        $connection = $this->em->getRepository(Connection::class)->find($connectionId);

        if(!$connection) {
            $this->notifier->send(new Notification('Connection not found'));
            return  false;
        }

        $networks = $connection->getNetworks();
        try {

            foreach ($networks as $network) {
                //next endpoint
                $endpoint = $this->em->getRepository(ConnectionEndpoint::class)->findOneBy(['name' => CasambiEndpoints::NETWORK_INFORMATION]);
                $network['X-Casambi-Session'] = $network['sessionId'];
                $requestParams = RequestHelper::buildRequest($network, $endpoint);

                try {
                    $response = $this->client->request($endpoint->getMethod(), $requestParams['uri'], $requestParams['options']);

                    $networkInfo = json_decode($response->getBody(), true);

                    foreach ($networkInfo['units'] as $unit) {
                        try {
                            $endpoint = $this->em->getRepository(ConnectionEndpoint::class)->findOneBy(['name' => CasambiEndpoints::GET_IMAGE]);

                            //Download the image
                            //Todo: Change path to be injected
                            $requestParams = RequestHelper::buildRequest($network+['unitId' =>$unit['id']], $endpoint);
                                $imageFileResource = fopen('/var/www/octo_data_portal_develop/shared/public/uploads/image.jpeg', 'w+');
                                $this->client->request($endpoint->getMethod(), $requestParams['uri'],
                                    $requestParams['options'] + [RequestOptions::SINK => $imageFileResource]);
                                $imageData = file_get_contents('/var/www/octo_data_portal_develop/shared/public/uploads/image.jpeg');
                                $image = 'data:image/' . 'jpeg' . ';base64,' . base64_encode($imageData);
                        }catch (ClientException $e) {
                            $image = '';
                            } catch (\Exception $e) {
                            $this->logger->error($e->getTraceAsString());
                            $image = '';
                        }

                        $area = $unit['groupId']  > 0 ? $networkInfo['groups'][$unit['groupId']]['name'] : 'Not Grouped';

                        $device = (new Device())
                            ->setName($unit['name'])
                            ->setDeviceType($this->em->getRepository(DeviceType::class)->findOneBy(['type' => $unit['type'], 'brand' => 'Casambi']))
                            ->setArea(
                                $this->em->getRepository(Area::class)->findOneBy(['name' => $area])?:
                                ((new Area())->setName($area))
                            )
                        ->setImage($image)
                        ->setDeviceMapping($unit);
                        $this->em->persist($device);
                        $this->em->flush();
                    }




                }catch (ClientException $e) {
                    $this->logger->error($e->getTraceAsString());
                    $this->notifier->send(new Notification('Sorry, there\'s been a problem getting your network data',['browser']));
                }
            }
            return true;

        } catch (ClientException $e) {
            $this->logger->error($e->getTraceAsString());
            $this->notifier->send(new Notification($e->getCode() === Response::HTTP_UNAUTHORIZED ? 'Please check the details that you have entered' : 'Sorry, we\'ve not been able to authenticate you with Casambi',['browser']));
        } catch (GuzzleException $e) {
            $this->logger->error($e->getTraceAsString());
        }
        return  false;
    }

    /**
     * Updates the devices with the provided mappings
     *
     * @param array $data
     * @param array $devices
     * @return bool
     */
    public function mapDevices(array $data, array $devices): bool
    {
        // TODO: Implement mapDevices() method.
        //Update the mapping field in each device with the information needed to perform the control requests
        foreach ($this->getOctoDevices() as $octoDevice){
            if(key_exists($octoDevice->getId(), $data)) {
            }
        }

        return  false;
    }

    /**
     * Gets the current status for each device
     *
     * @param int $connectionId
     * @return array|null
     */
    public function getDevicesState(int $connectionId): ?array
    {
        $connection = $this->em->getRepository(Connection::class)->find($connectionId);

        if(!$connection) {
            $this->notifier->send(new Notification('Connection not found'));
            return  null;
        }

        $networks = $connection->getNetworks();

        //next endpoint
        $endpoint = $this->em->getRepository(ConnectionEndpoint::class)->findOneBy(['name' => CasambiEndpoints::NETWORK_INFORMATION]);

        $states = [];

        foreach ($networks as $network) {
            $network['X-Casambi-Session'] = $network['sessionId'];
            $requestParams = RequestHelper::buildRequest($network, $endpoint);
            try {
                $response = $this->client->request($endpoint->getMethod(), $requestParams['uri'], $requestParams['options']);

                $states.= json_decode($response->getBody(), true)['units'];
            }catch (ClientException $e) {
                $this->logger->error($e->getTraceAsString());
            } catch (GuzzleException $e) {
            }
        }

        return $states;
    }

    /**
     * Gets a list of octo devices for the Casambi brand
     *
     * @return Device[]|null
     */
    public function getOctoDevices(): ?array
    {
        return $this->em->getRepository(Device::class)->findByBrand('Casambi');
    }
}