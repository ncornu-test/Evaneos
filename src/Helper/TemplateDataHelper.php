<?php


namespace Helper;

use DestinationRepository;
use Quote;
use QuoteRepository;
use SiteRepository;
use Template;
use User;

class TemplateDataHelper
{
    static function getTemplateData(Template $template, int $quoteId, int $siteId, int $destinationId, User $user): Array
    {
        $templateVariables = [];

        //$quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;
        $quoteFromRepository = QuoteRepository::getInstance()->getById($quoteId);
        $destinationOfQuote = DestinationRepository::getInstance()->getById($destinationId);
        $siteOfQuote = SiteRepository::getInstance()->getById($siteId);
        $templateSubjectAndContent = $template->subject . ' ' .$template->content;

        $containsSummaryHtml = strpos($template->content, '[quote:summary_html]');
        $containsSummary     = strpos($template->content, '[quote:summary]');

        // Destination name
        if (strpos($templateSubjectAndContent, '[quote:destination_name]') !== false)
            $templateVariables['[quote:destination_name]'] = $destinationOfQuote->countryName;

        // Summary
        if ($containsSummaryHtml !== false || $containsSummary !== false) {
            if ($containsSummaryHtml !== false) {
                $templateVariables['[quote:summary_html]'] = Quote::renderHtml($quoteFromRepository);
            }
            if ($containsSummary !== false) {
                $templateVariables['[quote:summary]'] = Quote::renderText($quoteFromRepository);
            }
        }

        // Destination link
        if (strpos($templateSubjectAndContent, '[quote:destination_link]') !== false)
            $text = str_replace('[quote:destination_link]', $siteOfQuote->url . '/' . $destinationOfQuote->countryName . '/quote/' . $quoteFromRepository->id, $text);

        // User;
        if ($user)
        {
            if (strpos($templateSubjectAndContent, '[user:first_name]') !== false)
                $templateVariables['[user:first_name]'] = ucfirst(mb_strtolower($user->firstname));
        }

        return $templateVariables;
    }
}