{*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*}

{extends file='page.tpl'}
{block name='page_content'}
	<script src="https://maps.google.com/maps/api/js?key={$setting.google_api|escape:'htmlall':'UTF-8'}"></script>
	<div id="storelocator">
		<div class="header">
			{if $banner}
			<img class="banner" src="{$banner|escape:'htmlall':'UTF-8'}" />
			{/if}
		</div>

		<div class="filter bh-sl-filters-container">
			<form id="bh-sl-user-location" method="post" action="#">
				<div class="row">
					<div class="col-md-2">
						<div id="country-filter" class="form-group bh-sl-filters">
							<label for="country">{l s='Country' mod='pickup'}</label>
							<select class="form-control" id="country" name="country_select">
								<option value="">{l s='All country' mod='pickup'}</option>
								{foreach from=$countries item=country}
									<option value="{$country.name|escape:'htmlall':'UTF-8'}" data-id="{$country.id_country|escape:'htmlall':'UTF-8'}">{$country.name|escape:'htmlall':'UTF-8'}</option>
								{/foreach}							
							</select>					
						</div>				
					</div>
					<div class="col-md-2">
						<div id="state-filter" class="form-group bh-sl-filters">
							<label for="state">{l s='State' mod='pickup'}</label>
							<select class="form-control" id="state" name="state_select">
								<option value="" data-fix="1">{l s='All state' mod='pickup'}</option>
								{foreach from=$states item=state}
									<option value="{$state.name|escape:'htmlall':'UTF-8'}" data-id="{$state.id_state|escape:'htmlall':'UTF-8'}" data-country="{$state.id_country|escape:'htmlall':'UTF-8'}">{$state.name|escape:'htmlall':'UTF-8'}</option>
								{/foreach}							
							</select>					
						</div>			
					</div>
					<div class="col-md-2">
						<div id="city-filter" class="form-group bh-sl-filters">
							<label for="city">{l s='City' mod='pickup'}</label>
							<select class="form-control" id="city" name="city_select">
								<option value="" data-fix="1">{l s='All city' mod='pickup'}</option>
								{foreach from=$cities item=city}
									<option value="{$city.city|escape:'htmlall':'UTF-8'}" data-state="{$city.state_id|escape:'htmlall':'UTF-8'}">{$city.city|escape:'htmlall':'UTF-8'}</option>
								{/foreach}	
							</select>
						</div>					
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="maxdistance">{l s='Distance' mod='pickup'}</label>
							<select class="form-control" id="maxdistance" name="maxdistance">
								<option value="">{l s='Select' mod='pickup'}</option>
								{foreach from=$radius item=rd}
									<option value="{$rd|escape:'htmlall':'UTF-8'}">{$rd|escape:'htmlall':'UTF-8'} {$setting.radius_unit|escape:'htmlall':'UTF-8'}</option>
								{/foreach}							
							</select>					
						</div>			
					</div>
					<div class="col-md-4" style="padding-top:28px;">
						{if $setting.enable_my_location}
						<button class="btn btn-default" id="currentLoc" type="button">{l s='use my current location' mod='pickup'}</button>
						{/if}	
						<button class="btn btn-primary" id="bh-sl-submit">{l s='Search' mod='pickup'}</button>
					</div>
				</div>
			</form>
					
			
		</div>
		
		<div class="bh-sl-container">
			  <div id="bh-sl-map-container" class="bh-sl-map-container">
				<div id="bh-sl-map" class="bh-sl-map"></div>
				<div class="bh-sl-loc-list">
				  <ul class="list"></ul>
				</div>
				<div class="bh-sl-pagination-container">
					<ol class="bh-sl-pagination"></ol>
				</div>				
			  </div>
		</div>
		
	</div>
{/block}