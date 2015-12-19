<?php

namespace PJM\AppBundle\Services;

class Trads
{
    public function getNums($fams) {
        return array_map(function($fam) {
            if ($fam === 'XIII') {
                return 13;
            }

            $len = strlen($fam);

            if ($len > 1 && $fam[$len-1] === '!') {
                return $this->fact((int)substr($fam, 0, $len - 1));
            }

            if ($len > 2 && substr($fam, -3) === 'bis') {
                return (int)$fam + 1;
            }

            return (int)$fam;
        },  preg_split('/[\s:#-]+/', $fams));
    }

    private function fact($n) {
        return $n ? ($n * $this->fact($n-1)) : 1;
    }
}
