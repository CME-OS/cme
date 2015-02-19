<?php
/**
 * @author  oke.ugwu
 */

namespace Cme\Helpers;

use Illuminate\Support\Facades\Config;

class CampaignHelper
{
  private static $_placeHolders;

  public static function compileMessage($campaign, $brand, $subscriber)
  {
    if(self::$_placeHolders == null)
    {
      $columns = array_keys((array)$subscriber);
      foreach($columns as $c)
      {
        self::$_placeHolders[$c] = "[$c]";
      }

      //add brand attributes as placeholders too
      $columns = array_keys($brand->attributesToArray());
      foreach($columns as $c)
      {
        self::$_placeHolders[$c] = "[$c]";
      }
    }

    //parse and compile message (replacing placeholders if any)
    $html = $campaign->html_content;
    $text = $campaign->text_content;
    foreach(self::$_placeHolders as $prop => $placeHolder)
    {
      $replace = false;
      if(property_exists($subscriber, $prop))
      {
        $replace = $subscriber->$prop;
      }
      elseif(property_exists($brand, $prop))
      {
        if($prop != 'brand_unsubscribe_url')
        {
          $replace = $brand->$prop;
        }
      }

      if($replace !== false)
      {
        $html = str_replace($placeHolder, $replace, $html);
        $text = str_replace($placeHolder, $replace, $text);
      }
    }

    $data = [
      'campaignId'            => $campaign->id,
      'listId'                => $campaign->list_id,
      'subscriberId'          => $subscriber->id,
      'brand_unsubscribe_url' => $brand->brand_unsubscribe_url
    ];
    $html = self::_insertTrackers($html, $data);

    $message = new \stdClass();
    $message->html = $html;
    $message->text = $text;
    return $message;
  }


  private static function _getUnsubscribeUrl($url, $data)
  {
    $url = self::_generateTrackLink('unsubscribe', $data)
      . "/" . base64_encode($url);
    return $url;
  }

  private static function _insertTrackers($html, $data)
  {
    //wrap html in <cme> tags, so we can extract our original content after
    //messing with it in DOM
    $html = "<cme>".$html."</cme>";

    //find all links in campaign and track them
    $dom = new \DOMDocument();
    @$dom->loadHTML($html);
    $clickLink = self::_generateTrackLink('click', $data);
    foreach($dom->getElementsByTagName('a') as $node)
    {
      $oldHref = $node->getAttribute('href');
      $newHref = $clickLink . "/" . base64_encode($oldHref);
      $node->setAttribute('href', $newHref);
    }
    $html = $dom->saveHTML();

    //grab original content between <cme> tags
    $html = strstr($html, '<cme>');
    $html = strstr($html, '</cme>', true);
    $html = trim(str_replace('<cme>', '', $html));

    //track un-subscribe link
    $replace = self::_getUnsubscribeUrl(
      $data['brand_unsubscribe_url'],
      $data
    );
    $html    = str_replace('[brand_unsubscribe_url]', $replace, $html);

    //append pixel to html content, so we can track opens
    $pixelUrl = self::_generateTrackLink('open', $data);
    $html .= '<img src="' . $pixelUrl
      . '" style="display:none;" height="1" width="1" />';

    return $html;
  }

  private static function _generateTrackLink($type, $data)
  {
    $domain = Config::get('app.domain');
    return "http://" . $domain . "/track/" . $type . "/" . $data['campaignId']
    . "_" . $data['listId'] . "_" . $data['subscriberId'];
  }

  public static function getPriority($priority)
  {
    switch($priority)
    {
      case 1:
        $name = "Low";
        break;
      case 2:
        $name = "Normal";
        break;
      case 3:
        $name = "Medium";
        break;
      case 4:
        $name = "High";
        break;
      default:
        $name = "Unknowm";
    }

    return $name;
  }
}
