<?php

namespace App\Controller;

use App\Service\Twitch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    const GAME_APEX_ID = 511224;
    const GAME_FORTNITE_ID = 33214;

    const GAME_LITTLE_IMG_SIZE = '285x380';
    const GAME_BIG_IMG_SIZE = '900x1180';
    const STREAM_IMG_SIZE = '400x225';

    /**
     * @param Twitch $twitch
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(Twitch $twitch)
    {
        $gamesResponseContent = $twitch->getGames([self::GAME_APEX_ID, self::GAME_FORTNITE_ID]);
        $apexStreamsResponseContent = $twitch->getStreams([self::GAME_APEX_ID]);
        $fortniteStreamsResponseContent = $twitch->getStreams([self::GAME_FORTNITE_ID]);

        $nbFortniteViewers = $nbApexViewers = 0;
        foreach ($fortniteStreamsResponseContent->data as $stream) {
            $nbFortniteViewers += $stream->viewer_count;
        }
        foreach ($apexStreamsResponseContent->data as $stream) {
            $nbApexViewers += $stream->viewer_count;
        }
        
        $apexPercent = $nbApexViewers * 100 / ($nbApexViewers + $nbFortniteViewers);

        $apex = $gamesResponseContent->data[0]->id == self::GAME_APEX_ID ? $gamesResponseContent->data[0] : $gamesResponseContent->data[1];
        $fortnite = $gamesResponseContent->data[0]->id == self::GAME_FORTNITE_ID ? $gamesResponseContent->data[0] : $gamesResponseContent->data[1];
        $winner = $apexPercent >= 50 ? $apex : $fortnite;

        return $this->render('index.html.twig', [
            'nbApexViewers' => $nbApexViewers,
            'nbFortniteViewers' => $nbFortniteViewers,
            'apexPercent' => round($apexPercent),
            'winner' => $winner,
            'apex' => $apex,
            'fortnite' => $fortnite,
            'imgGameWinner' => str_replace('{width}x{height}', self::GAME_BIG_IMG_SIZE, $winner->box_art_url),
            'imgFornite' => str_replace('{width}x{height}', self::GAME_LITTLE_IMG_SIZE, $fortnite->box_art_url),
            'imgApex' => str_replace('{width}x{height}', self::GAME_LITTLE_IMG_SIZE, $apex->box_art_url),
            'fortniteStreams' => array_slice($fortniteStreamsResponseContent->data, 0, 8),
            'apexStreams' => array_slice($apexStreamsResponseContent->data, 0, 8),
            'thumbnailSize' => self::STREAM_IMG_SIZE
        ]);
    }
}