{*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*}
<li class="item">
	<div class="row">
		<div class="col-md-12 selection">
			<div class="custom-radio float-xs-left">
				<input type="radio" name="store_id" id="store_id_{$store.id|escape:'htmlall':'UTF-8'}" 
				data-address="{$store.address|escape:'htmlall':'UTF-8'} {$store.city|escape:'htmlall':'UTF-8'}, {$store.state|escape:'htmlall':'UTF-8'} {$store.postal|escape:'htmlall':'UTF-8'}" 
				data-name="{$store.name|escape:'htmlall':'UTF-8'}"
				data-deliverystart="{$store.deliverydate_start|escape:'htmlall':'UTF-8'}"
				data-deliveryend="{$store.deliverydate_end|escape:'htmlall':'UTF-8'}"
				data-cost="{$store.shipping_cost|escape:'htmlall':'UTF-8'}"
				value="{$store.id|escape:'htmlall':'UTF-8'}" /><span></span>
			</div>
			<h6 class="name"><label for="store_id_{$store.id|escape:'htmlall':'UTF-8'}">{$store.name|escape:'htmlall':'UTF-8'}</label></h6>
		</div>
		<div class="col-md-6">
			<div>								
				<div class="contact">
					<label>{l s='address' mod='pickup'}</label>
					{$store.address|escape:'htmlall':'UTF-8'} {$store.city|escape:'htmlall':'UTF-8'} {$store.state|escape:'htmlall':'UTF-8'} {$store.country|escape:'htmlall':'UTF-8'}					
				</div>
				{if $store.landmark}
				<div class="contact">
					<label>{l s='landmark' mod='pickup'}</label>
					{$store.landmark|escape:'htmlall':'UTF-8'}			
				</div>
				{/if}
				<div class="contact">
					<label>{l s='contact information' mod='pickup'}</label>
					{$store.phone|escape:'htmlall':'UTF-8'} 
					{if $store.email} - {$store.email|escape:'htmlall':'UTF-8'}{/if}
				</div>
				<div class="contact working">
					<label>{l s='Opening Hours' mod='pickup'}</label>
					{if $store.working_hours == 1}
						{l s='EveryDay' mod='pickup'} {$store.timing[0]|escape:'htmlall':'UTF-8'} - {$store.timing[1]|escape:'htmlall':'UTF-8'}
					{elseif $store.working_hours == 2}
						
						{if $store.timing[0]->isActive}
							{l s='Mon' mod='pickup'} {$store.timing[0]->timeFrom|escape:'htmlall':'UTF-8'} - {$store.timing[0]->timeTill|escape:'htmlall':'UTF-8'}; 
						{/if}
						{if $store.timing[1]->isActive}
							{l s='Tue' mod='pickup'} {$store.timing[1]->timeFrom|escape:'htmlall':'UTF-8'} - {$store.timing[1]->timeTill|escape:'htmlall':'UTF-8'}; 
						{/if}
						{if $store.timing[2]->isActive}
							{l s='Wed' mod='pickup'} {$store.timing[2]->timeFrom|escape:'htmlall':'UTF-8'} - {$store.timing[2]->timeTill|escape:'htmlall':'UTF-8'}; 
						{/if}
						{if $store.timing[3]->isActive}
							{l s='Thu' mod='pickup'} {$store.timing[3]->timeFrom|escape:'htmlall':'UTF-8'} - {$store.timing[3]->timeTill|escape:'htmlall':'UTF-8'}; 
						{/if}
						{if $store.timing[4]->isActive}
							{l s='Fri' mod='pickup'} {$store.timing[4]->timeFrom|escape:'htmlall':'UTF-8'} - {$store.timing[4]->timeTill|escape:'htmlall':'UTF-8'}; 
						{/if}
						{if $store.timing[5]->isActive}
							{l s='Sat' mod='pickup'} {$store.timing[5]->timeFrom|escape:'htmlall':'UTF-8'} - {$store.timing[5]->timeTill|escape:'htmlall':'UTF-8'}; 
						{/if}
						{if $store.timing[6]->isActive}
							{l s='Sun' mod='pickup'} {$store.timing[6]->timeFrom|escape:'htmlall':'UTF-8'} - {$store.timing[6]->timeTill|escape:'htmlall':'UTF-8'}; 
						{/if}
					{/if}
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="contact">
				<label>{l s='Delivery Time' mod='pickup'}</label>
				 {l s='Delivery in' mod='pickup'} {$store.deliverytime_start|escape:'htmlall':'UTF-8'} {l s='to' mod='pickup'} {$store.deliverytime_end|escape:'htmlall':'UTF-8'} {l s='Days' mod='pickup'}
			</div>	
			<div class="contact">
				<label>{l s='Shipping Cost' mod='pickup'}</label>
				{$store.shipping_cost|escape:'htmlall':'UTF-8'}
			</div>
			<div class="contact">
				<label>{l s='Payment Methods' mod='pickup'}</label>
				{$store.paymentmethod|escape:'htmlall':'UTF-8'}
			</div>							
		</div>	
		<div class="clearfix"></div>
	</div>
</li>