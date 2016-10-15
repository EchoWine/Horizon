<?php

namespace WT\Model;

interface Resource{

	public function toArrayComplete();

	public function fillFromDatabaseApi($response,$container);
}