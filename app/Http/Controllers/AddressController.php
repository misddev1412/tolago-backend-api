<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\Address\FindLatLangFromAddressRequest;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;

class AddressController extends Controller
{
    public function getLatAndLangFromAddress(FindLatLangFromAddressRequest $request)
    {
        $key = env('GOOGLEAPIS_KEY');
        $address = $request->address;
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&key=' . $key;
        $locations = [];
        $response = Http::get($url)->collect('results');
        if ($response) {
            $response->pluck('geometry')->each(function ($geometry) use (&$locations) {
                $locations[] = [
                    'lat' => $geometry['location']['lat'],
                    'lng' => $geometry['location']['lng'],
                ];
            });
            foreach ($locations as $index => $location) {
                $locations[$index]['address'] = $response->pluck('formatted_address')[$index];
            }

        }
        return Response::generateResponse(HttpStatusCode::OK, '', $locations);
    }
}
