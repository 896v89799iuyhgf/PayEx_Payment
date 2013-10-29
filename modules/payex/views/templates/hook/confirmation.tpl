<p>{l s='Your order on' mod='payex'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='payex'}
    <br /><br />
    {l s='You have chosen the payex method.' mod='payex'}
    <br /><br /><span class="bold">{l s='Your order will be sent very soon.' mod='payex'}</span>
    <br /><br />{l s='For any questions or for further information, please contact our' mod='payex'}
    <a href="{$link->getPageLink('contact-form', true)|escape:'html'}">{l s='customer support' mod='payex'}</a>.
</p>
