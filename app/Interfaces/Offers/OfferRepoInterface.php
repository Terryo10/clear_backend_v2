<?php

namespace App\Interfaces\Offers;

interface OfferRepoInterface
{

    public function create(array $data, $options);

    public function update(array $data, $options);

    public function acceptOffer($offerId, $data);

    public function sign($offerId, $data);
}
