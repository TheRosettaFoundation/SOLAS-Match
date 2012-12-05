<?php
/*
    This class is used to read humorous tips from a file
    on the local FS and select one for display
*/

class TipSelector
{
    var $_tip_list = array();

    public function TipSelector()
    {
        $this->_tip_list = array();

        $tip_file = __DIR__.'/../../resources/tips/tips.txt';
        $handle = fopen($tip_file, 'r');

        while($tmp = fgets($handle)) {
            if($tmp != '') {
                $this->_tip_list[] = $tmp;
            }
        }
    }

    public function selectTip()
    {
        return $this->_tip_list[rand(0, count($this->_tip_list) - 1)];
    }
}
