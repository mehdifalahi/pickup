{*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*}

{extends file='page.tpl'}
{block name='head_seo_title'}{if $store.meta_title}{$store.meta_title|escape:'htmlall':'UTF-8'}{else}{$store.name|escape:'htmlall':'UTF-8'}{/if}{/block}
{block name='head_seo_description'}{$store.meta_description|escape:'htmlall':'UTF-8'}{/block}
{block name='head_seo_keywords'}{$store.meta_keyword|escape:'htmlall':'UTF-8'}{/block}
{block name='page_content'}
	<script src="https://maps.googleapis.com/maps/api/js?key={$setting.google_api|escape:'htmlall':'UTF-8'}&callback=initMap&libraries=&v=weekly" defer></script>

	<div id="store">
		<div id="mapstore"></div>
		<div class="header">
			<h1 class="name">{$store.name|escape:'htmlall':'UTF-8'}</h1>
			<div class="row">
				<div class="col-md-{if $setting.display_phone}3{else}4{/if} block">
					<div class="picon col-md-2"><i class="fa fa-map-marker-alt"></i></div>
					<div class="pdata col-md-10">
					{$store.address|escape:'htmlall':'UTF-8'} {$store.city|escape:'htmlall':'UTF-8'} {$store.state|escape:'htmlall':'UTF-8'} {$store.country|escape:'htmlall':'UTF-8'} 
					</div>
				</div>
				<div class="col-md-{if $setting.display_phone}3{else}4{/if} block">
					<div class="picon col-md-2"><i class="fa fa-directions"></i></div>
					<div class="pdata col-md-10"><a href="https://maps.google.com/maps?saddr=current+location&daddr={$store.address|escape:'htmlall':'UTF-8'} {$store.city|escape:'htmlall':'UTF-8'}, {$store.state|escape:'htmlall':'UTF-8'} {$store.postal|escape:'htmlall':'UTF-8'}" target="_blank">{l s='Get directions' mod='pickup'}</a></div>
				</div>
				{if $setting.display_phone}
				<div class="col-md-3 block">
					<div class="picon col-md-2"><i class="fa fa-phone-alt"></i></div>
					<div class="pdata col-md-10">{$store.phone|escape:'htmlall':'UTF-8'}</div>
				</div>
				{/if}
				<div class="col-md-{if $setting.display_phone}3{else}4{/if} block">
					<div class="picon col-md-2"><i class="fa fa-clock"></i></div>
					<div class="pdata col-md-10">
						<div class="workingtime_now"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="desc">
			<div class="row">
				<div class="col-md-6 text">
					<h4>{l s='STORE DESCRIPTION' mod='pickup'}</h4>
					<div>{$store.description nofilter}{*This is HTML content*}</div>
					{if $store.email}
					<div class="email">
						<span><i class="fa fa-envelope"></i> {$store.email|escape:'htmlall':'UTF-8'}</span>
					</div>
					{/if}
					{if $store.website}
					<div class="website">
						<span><i class="fa fa-globe"></i> <a href="{$store.website|escape:'htmlall':'UTF-8'}" target="_blank">{l s='Website' mod='pickup'}</a></span>
					</div>
					{/if}
					<div class="social">
						<ul>
						{if $store.instagram}<li><a href="{$store.instagram|escape:'htmlall':'UTF-8'}"><img src="{$url|escape:'htmlall':'UTF-8'}modules/pickup/views/img/instagram.png" /></a></li>{/if}
						{if $store.facebook}<li><a href="{$store.facebook|escape:'htmlall':'UTF-8'}"><img src="{$url|escape:'htmlall':'UTF-8'}modules/pickup/views/img/facebook.png" /></a></li>{/if}
						{if $store.skype}<li><a href="skype:{$store.skype|escape:'htmlall':'UTF-8'}?call"><img src="{$url|escape:'htmlall':'UTF-8'}modules/pickup/views/img/skype.png" /></a></li>{/if}
						{if $store.whatsapp}<li><a href="https://wa.me/{$store.whatsapp|escape:'htmlall':'UTF-8'}"><img src="{$url|escape:'htmlall':'UTF-8'}modules/pickup/views/img/whatsapp.png" /></a></li>{/if}
						{if $store.twitter}<li><a href="{$store.twitter|escape:'htmlall':'UTF-8'}"><img src="{$url|escape:'htmlall':'UTF-8'}modules/pickup/views/img/twitter.png" /></a></li>{/if}
						</ul>						
					</div>
				</div>
				<div class="col-md-6 img">
				{if $image}
					<img src="{$image|escape:'htmlall':'UTF-8'}" />
				{/if}
				</div>
			</div>
		</div>
		<div class="hours">
			<div class="row">
				<div class="col-md-6 offset-md-3 text-center">
					<h4 style="text-align:center;">{l s='HOURS' mod='pickup'}</h4>
					<table class="table table-striped">
						<tr>
							<td class="name">{l s='Monday' mod='pickup'}</td>
							<td class="value">
								{if $store.working_hours == 1}
									{$timing[0]|escape:'htmlall':'UTF-8'} - {$timing[1]|escape:'htmlall':'UTF-8'} 
								{else}						
									{if $timing[0]->isActive}
										{$timing[0]->timeFrom|escape:'htmlall':'UTF-8'} - {$timing[0]->timeTill|escape:'htmlall':'UTF-8'} 
									{else}
										{l s='Closed' mod='pickup'}
									{/if}
								{/if}
							</td>
						</tr>
						<tr>
							<td class="name">{l s='Tuesday' mod='pickup'}</td>
							<td class="value">
								{if $store.working_hours == 1}
									{$timing[0]|escape:'htmlall':'UTF-8'} - {$timing[1]|escape:'htmlall':'UTF-8'} 
								{else}						
									{if $timing[1]->isActive}
										{$timing[1]->timeFrom|escape:'htmlall':'UTF-8'} - {$timing[1]->timeTill|escape:'htmlall':'UTF-8'} 
									{else}
										{l s='Closed' mod='pickup'}
									{/if}
								{/if}							
							</td>
						</tr>
						<tr>
							<td class="name">{l s='Wednesday' mod='pickup'}</td>
							<td class="value">
								{if $store.working_hours == 1}
									{$timing[0]|escape:'htmlall':'UTF-8'} - {$timing[1]|escape:'htmlall':'UTF-8'} 
								{else}						
									{if $timing[2]->isActive}
										{$timing[2]->timeFrom|escape:'htmlall':'UTF-8'} - {$timing[2]->timeTill|escape:'htmlall':'UTF-8'} 
									{else}
										{l s='Closed' mod='pickup'}
									{/if}
								{/if}							
							</td>
						</tr>
						<tr>
							<td class="name">{l s='Thursday' mod='pickup'}</td>
							<td class="value">
								{if $store.working_hours == 1}
									{$timing[0]|escape:'htmlall':'UTF-8'} - {$timing[1]|escape:'htmlall':'UTF-8'} 
								{else}						
									{if $timing[3]->isActive}
										{$timing[3]->timeFrom|escape:'htmlall':'UTF-8'} - {$timing[3]->timeTill|escape:'htmlall':'UTF-8'} 
									{else}
										{l s='Closed' mod='pickup'}
									{/if}
								{/if}							
							</td>
						</tr>
						<tr>
							<td class="name">{l s='Friday' mod='pickup'}</td>
							<td class="value">
								{if $store.working_hours == 1}
									{$timing[0]|escape:'htmlall':'UTF-8'} - {$timing[1]|escape:'htmlall':'UTF-8'} 
								{else}						
									{if $timing[4]->isActive}
										{$timing[4]->timeFrom|escape:'htmlall':'UTF-8'} - {$timing[4]->timeTill|escape:'htmlall':'UTF-8'} 
									{else}
										{l s='Closed' mod='pickup'}
									{/if}
								{/if}							
							</td>
						</tr>
						<tr>
							<td class="name">{l s='Saturday' mod='pickup'}</td>
							<td class="value">
								{if $store.working_hours == 1}
									{$timing[0]|escape:'htmlall':'UTF-8'} - {$timing[1]|escape:'htmlall':'UTF-8'} 
								{else}						
									{if $timing[5]->isActive}
										{$timing[5]->timeFrom|escape:'htmlall':'UTF-8'} - {$timing[5]->timeTill|escape:'htmlall':'UTF-8'} 
									{else}
										{l s='Closed' mod='pickup'}
									{/if}
								{/if}							
							</td>
						</tr>
						<tr>
							<td class="name">{l s='Sunday' mod='pickup'}</td>
							<td class="value">
								{if $store.working_hours == 1}
									{$timing[0]|escape:'htmlall':'UTF-8'} - {$timing[1]|escape:'htmlall':'UTF-8'} 
								{else}						
									{if $timing[6]->isActive}
										{$timing[6]->timeFrom|escape:'htmlall':'UTF-8'} - {$timing[6]->timeTill|escape:'htmlall':'UTF-8'} 
									{else}
										{l s='Closed' mod='pickup'}
									{/if}
								{/if}							
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	
    <script>
	var lat = {$store.lat|escape:'htmlall':'UTF-8'};
	var lng = {$store.lng|escape:'htmlall':'UTF-8'};
      function initMap() {
        const myLatLng = { lat: lat, lng: lng };
        const map = new google.maps.Map(document.getElementById("mapstore"), {
          zoom: 4,
          center: myLatLng,
		  disableDefaultUI: true,
        });
        new google.maps.Marker({
          position: myLatLng,
          map,
        });
      }
    </script>
	
{/block}