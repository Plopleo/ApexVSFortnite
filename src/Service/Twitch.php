<?php

namespace App\Service;

use NewTwitchApi\HelixGuzzleClient;
use NewTwitchApi\NewTwitchApi;

class Twitch
{

    private $clientId;
    private $clientSecret;

    /** @var NewTwitchApi|null  */
    private $api = null;

    public function __construct($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return NewTwitchApi
     */
    public function getApi()
    {
        if ($this->api !== null) {
            return $this->api;
        }

        $helixGuzzleClient = new HelixGuzzleClient($this->clientId);
        return new NewTwitchApi($helixGuzzleClient, $this->clientId, $this->clientSecret);
    }

    /**
     * Return streams infos
     *
     * @param array $streamsId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStreams(array $streamsId)
    {
        try {
            $streams = $this->getApi()->getStreamsApi()->getStreams([], [], $streamsId, [], []);
        } catch (\Exception $e) {

        }

        return json_decode($streams->getBody()->getContents());
    }

    /**
     * Return games infos
     *
     * @param array $gamesId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGames(array $gamesId)
    {
        try {
            $games = $this->getApi()->getGamesApi()->getGames($gamesId);
        } catch (\Exception $e) {
            // Handle error appropriately for your application
        }

        return json_decode($games->getBody()->getContents());
    }
}