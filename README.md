# Currency Layer plugin for Craft CMS 3.x

API integration with https://currencylayer.com/

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require ournameismud/currency-layer

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Currency Layer.

## Currency Layer Overview

This is a plugin layer for the [Currency Layer API](https://currencylayer.com/) that lets you store and retrieve currency values locally. If you use this in conjunction with Craft Commerce it will populate the conversion rate values for corresponding Payment Currencies.


## Configuring Currency Layer

To use this plugin you will require an API key from Currency Layer. A basic account is free but rate-limited to [250 Requests/mo](https://currencylayer.com/product). 

Options available in the plugin can be saved in `Plugin > Settings` or via [a config file](https://docs.craftcms.com/v3/extend/plugin-settings.html#overriding-setting-values).

The plugin settings available are:

- *apiKey* the Currency Layer API key
- *primaryCurrency* the primary currency you want to use as the base for conversions
- *apiCache* whether to cache the response (recommended)
- *cacheDuration* how long you want the cache to be stored/saved for

## Using Currency Layer

You can use Currency Layer in one of two ways. The first way is via an `action`. 
This is best used in conjunction with a CRON job to fetch the results periodically. The action can be triggered via a URL as follows:

`http://your.domain.com/actions/currency-layer/currency/fetch?currencies=EUR,GBP,USD` with the currencies parameter being a comma-separated list of currencies you wish to retrieve. Note that the base (free) version of Currency Layer will always default to USD as the source.

If you are using Craft Commerce this action will save the conversion rates directly into your Commerce settings. Otherwise the conversion values are available via the `craft.currencyLayer.fetch()` variable:

`craft.currencyLayer.fetch(request,type)` where `request` is required and should be an object of key:value attributes corresponding to the endpoints for the [Currency Layer API](https://currencylayer.com/documentation) you require (please note that many of the endpoints are limited to paid-for accounts). The `type` parameter corresponds to the type of query you wish to make (defaults to `live`).

For example, `{% set data = craft.currencyLayer.fetch({ currencies:'EUR,GBP,USD' }) %}` would fetch the following endpoint:
`https://apilayer.net/api/live?currencies=EUR,GBP,USD&access_key=YOUR_ACCESS_KEY`. The response will be an array of values which provide multipliers based on the primary currency defined in your plugin settings. With this in mind we can build out a macro to perform some simple currency conversion, for example: 


    {%- macro convert(value = 1, currency) -%}
        {% set data = craft.currencyLayer.fetch({ currencies:'EUR,GBP,USD' }) %}
        {{- data[currency] * value -}}
    {%- endmacro -%}
    
    {% set GBP = 100 %}
    {% set EUR = _self.convert(100,'EUR') %}
    {% set USD = _self.convert(100,'USD') %}
    
    <dl>
        <dt>GBP</dt><dd>{{ GBP }}</dd>
        <dt>EUR</dt><dd>{{ EUR }}</dd>
        <dt>USD</dt><dd>{{ USD }}</dd>
    </dl>


The plugin is designed to work closely with Craft Commerce currencies. If currencies are already defined in Craft Commerce the option to select a primary currency is restricted to those available in Craft Commerce. If a new primary currency is selected in the plugin then the primary Craft Commerce currency is updated and vice versa. 

## Currency Layer Roadmap

Some things to do, and ideas for potential features:

* Release it

Brought to you by [cole007](http://ournameismud.co.uk/)