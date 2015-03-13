<?php

/**
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license
 * It is  available through the world-wide-web at this URL:
 * http://www.petala-azul.com/bsd.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to geral@petala-azul.com so we can send you a copy immediately.
 *
 * @package    Bvb_Grid
 * @copyright  Copyright (c)  (http://www.petala-azul.com)
 * @license    http://www.petala-azul.com/bsd.txt   New BSD License
 * @version    $Id: Csv.php 1147 2010-04-28 22:46:14Z bento.vilas.boas@gmail.com $
 * @author     Bento Vilas Boas <geral@petala-azul.com >
 */

class Bvb_Grid_Deploy_Csv extends Bvb_Grid implements Bvb_Grid_Deploy_DeployInterface
{

    protected $dir;

    const OUTPUT = 'csv';

    public $deploy;

    /**
     * Set true if data should be downloaded
     */
    protected $downloadData = null;

    /**
     * Set true if data should be stored
     */
    protected $storeData = null;

    /**
     * Storing file
     */
    protected $outFile = null;

    /*
     *
     *
     * Optimize performance by setting best value for $this->setPagination(?);
     * and setting options:
     * set_time_limit
     * memory_limit
     * download: send data to directly to user
     * save: save the file
     * ?dir:
     *
     * @param array $data
     */
    public function __construct ($options)
    {
        $this->setNumberRecordsPerPage(500);

        parent::__construct($options);
    }


    public function buildTitltesCsv ($titles)
    {

        $grid = '';
        foreach ($titles as $title) {

            $grid .= '"' . strip_tags($title['value']) . '",';
        }

        return substr($grid, 0, - 1) . "\n";

    }

    public function buildSqlexpCsv ($sql)
    {

        $grid = '';
        if (is_array($sql)) {

            foreach ($sql as $exp) {
                $grid .= '"' . strip_tags($exp['value']) . '",';
            }
        }

        return substr($grid, 0, - 1) . " \n";

    }

    public function buildGridCsv ($grids)
    {

        $grid = '';
        foreach ($grids as $value) {

            foreach ($value as $final) {
                $grid .= '"' . strip_tags($final['value']) . '",';
            }

            $grid = substr($grid, 0, - 1) . " \n";
        }

        return $grid;

    }

    /**
     * Depending on settings store to file and/or directly upload
     */
    protected function csvAddData ($data)
    {
        if ($this->downloadData) {
            // send first headers
            echo $data;
            flush();
            ob_flush();
        }
        if ($this->storeData) {
            // open file handler
            fwrite($this->outFile, $data);
        }
    }
    public function deploy ()
    {
        if (! in_array(self::OUTPUT, $this->_export)) {
            echo $this->__("You dont' have permission to export the results to this format");
            die();
        }
        $this->deploy['dir'] = rtrim($this->deploy['dir'], '/') . '/';
        // apply options
        if (isset($this->deploy['set_time_limit'])) {
            // script needs time to proces huge amount of data (important)
            set_time_limit($this->deploy['set_time_limit']);
        }
        if (isset($this->deploy['memory_limit'])) {
            // adjust memory_limit if needed (not very important)
            ini_set('memory_limit', $this->deploy['memory_limit']);
        }

        if (empty($this->deploy['name'])) {
            $this->deploy['name'] = date('H_m_d_H_i_s');
        }

        if (substr($this->deploy['name'], - 4) == '.csv') {
            $this->deploy['name'] = substr($this->deploy['name'], 0, - 4);
        }



        // decide if we should store data to file or send directly to user
        $this->downloadData = $this->deploy['download'] == 1 ? 1 : false;
        $this->storeData = $this->deploy['save'] == 1 ? 1 : false;

        // prepare data
        parent::deploy();


        if ($this->downloadData) {
            // send first headers
            header('Content-type: text/plain; charset=' . $this->getCharEncoding());
            header('Content-Disposition: attachment; filename="' .$this->deploy['name'] . '.csv"');
        }
        if ($this->storeData) {
            // open file handler
            $this->outFile = fopen($this->deploy['dir']. $this->deploy['name']. ".csv", "w");
        }

        // export header
        $this->csvAddData(self::buildTitltesCsv(parent::_buildTitles()));
        $i = 0;
        do {
            $i += $this->_pagination;
            $this->csvAddData(self::buildGridCsv(parent::_buildGrid()));
            $this->csvAddData(self::buildSqlexpCsv(parent::_buildSqlExp()));
            // get next data
            $this->getSource()->buildQueryLimit($this->_pagination, $i);
            $this->_result = $this->getSource()->execute();
        } while (count($this->_result));

        if ($this->storeData) {
            // close file handler
            fclose($this->outFile);
        } else {
            die();
        }

        die();
        return true;
    }

}
