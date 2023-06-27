{*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*}

<input type="hidden" id="pickup_fee" value="{$shipping_cost|escape:'htmlall':'UTF-8'}" />
<input type="hidden" id="cart_id" value="{$cart_id|escape:'htmlall':'UTF-8'}" />
<input type="hidden" id="setData" value="{$setData|escape:'htmlall':'UTF-8'}" />
<input type="hidden" id="data" value="{$data|escape:'html':'UTF-8'}" />
<input type="hidden" value="{$token|escape:'html':'UTF-8'}">

<div style="display:none;">
	<div id="station-popup" class="white-popup-block">
		<h4 class="header">{l s='Select a pickup station' mod='pickup'}</h4>
		<div class="filter">
			<div class="row">
				<div class="col-md-3">
					<div id="country-filter" class="form-group">
						<label for="country">{l s='Country' mod='pickup'}</label>
						<select class="form-control" id="country" name="country_select">
							<option value="">{l s='All country' mod='pickup'}</option>
							{foreach from=$countries item=country}
								<option value="{$country.name|escape:'htmlall':'UTF-8'}" data-id="{$country.id_country|escape:'htmlall':'UTF-8'}">{$country.name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}							
						</select>					
					</div>				
				</div>
				<div class="col-md-3">
					<div id="state-filter" class="form-group">
						<label for="state">{l s='State' mod='pickup'}</label>
						<select class="form-control" id="state" name="state_select">
							<option value="" data-fix="1">{l s='All state' mod='pickup'}</option>
							{foreach from=$states item=state}
								<option value="{$state.name|escape:'htmlall':'UTF-8'}" data-id="{$state.id_state|escape:'htmlall':'UTF-8'}" data-country="{$state.id_country|escape:'htmlall':'UTF-8'}">{$state.name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}							
						</select>					
					</div>			
				</div>
				<div class="col-md-3">
					<div id="city-filter" class="form-group">
						<label for="city">{l s='City' mod='pickup'}</label>
						<select class="form-control" id="city" name="city_select">
							<option value="" data-fix="1">{l s='All city' mod='pickup'}</option>
							{foreach from=$cities item=city}
								<option value="{$city.city|escape:'htmlall':'UTF-8'}" data-state="{$city.state_id|escape:'htmlall':'UTF-8'}">{$city.city|escape:'htmlall':'UTF-8'}</option>
							{/foreach}	
						</select>
					</div>					
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="radius">{l s='Distance' mod='pickup'}</label>
						<select class="form-control" id="radius" name="radius">
							<option value="">{l s='Select' mod='pickup'}</option>
							{foreach from=$radius item=rd}
								<option value="{$rd|escape:'htmlall':'UTF-8'}">{$rd|escape:'htmlall':'UTF-8'} {$setting.radius_unit|escape:'htmlall':'UTF-8'}</option>
							{/foreach}							
						</select>					
					</div>			
				</div>
			</div>
		</div>
		
		
		<div id="stations">
			<h5>{l s='pickup stations near you' mod='pickup'}</h5>
			<div class="store-list">
				<ul>
					{foreach from=$stores item=store}
						{include file="./pickup_item.tpl"}
					{/foreach}
				</ul>
			</div>
			<div class="action">				
				<a id="select_direction" class="btn btn-secondary">{l s='See Pickup Direction' mod='pickup'}</a>
				<a id="select_store" class="btn btn-primary">{l s='Select This Pickup Station' mod='pickup'}</a>
				<div class="clearfix"></div>
			</div>			
		</div>
	</div>
</div>