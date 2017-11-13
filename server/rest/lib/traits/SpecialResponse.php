<?php

namespace lib\traits;

trait SpecialResponse
{
    private function specialResponse($status, $data = '')
    {
        return ['status' => $status, 'data' => $data];
    }
}