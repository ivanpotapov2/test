<?php

namespace application;
include('Adapter.php');
/**
 * Class Loader
 * @package application
 * TODO some unit tests
 */
class Loader
{
    const MARKET_TYPE_EU = 'eu';
    const MARKET_TYPE_US = 'us';

    /**
     * @var string
     */
    private $marketType;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var array
     */
    private $markets;

    /**
     * Loader constructor.
     */
    public function __construct()
    {
       $this->markets = Adapter::getInstance()->selectAll('SELECT id_value FROM markets');
        $this->markets = array_map(
            function ($item) {
                return $item['id_value'];
            },
            $this->markets
        );
    }

    /**
     * @param string $file
     */
    public function load($file)
    {
        Logger::getInstance()->info("Starting to load file $file");

        if (!file_exists($file)) {
            throw new \InvalidArgumentException('File ' . $file . ' doesn\'t exist');
        }

        $this->setMarketTypeAndDate($file);

        $handle = fopen($file, "r");

        $fileContent = [];
        while (($data = fgetcsv($handle, "1000", ",")) !== false) {
            $fileContent[] = $data;			  
        }

        unset($fileContent[0]);
        $this->parse($fileContent);

        Logger::getInstance()->info("File load is finished");
    }

    /**
     * @param string $file
     */
  private function setMarketTypeAndDate($file)
    {
        preg_match('/market.(eu|us).(\d+)$/', $file, $matches);

        if (empty($matches[1])) {
            throw new \InvalidArgumentException('Unknown market type');
        }

        $this->marketType = $matches[1];

        try {
            $this->date = new \DateTime($matches[2]);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Wrong date format in filename');
        }
    }

    /**
     * @param array $content
     */
    private function parse($content)
    {
        Logger::getInstance()->info("Starting to parse file");

        $count = 0;
        foreach ($content as $row) {
			
            $idValue = $this->marketType == self::MARKET_TYPE_EU ? $row[0] : $row[6];
            if (!in_array($idValue, $this->markets)) {
                continue;
            }

            $price = ltrim($row[1], '0');
            $isNoon = $row[5];
            $date = $this->date->format('Y-m-d');

            $query = "INSERT INTO `market_data` (`id_value` , `price`, `is_noon`, `update_date`) VALUES (?, ?, ?, ?)";
            Adapter::getInstance()->exec($query, [$idValue, $price, $isNoon, $date]);
            $count++;
        }

        Logger::getInstance()->info("File parsing is finished, $count records saved in database");
    
	}
}
