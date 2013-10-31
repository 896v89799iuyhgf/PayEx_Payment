{capture name=path}{l s='Order confirmation'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{$tran_status}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}

<p> {l s='There was an issue with the transaction' mod='payex'}<br /><br />
    {l s='Your order on' mod='payex'} <span class="bold">{$shop_name}</span> {l s='is not complete.' mod='payex'}
    <br /><br />
    {l s='You have chosen the payex method.' mod='payex'}
    <br /><br />{l s='For any questions or for further information, please contact our' mod='payex'}
    <a href="{$link->getPageLink('contact-form', true)|escape:'html'}">{l s='customer support' mod='payex'}</a>.
</p>
{$HOOK_PAYMENT_RETURN}

<br />
{if $is_guest}
    <p>{l s='Your order ID is:'} <span class="bold">{$id_order_formatted}</span> . {l s='Your order ID has been sent via email.'}</p>
    <a href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order}&email={$email}")|escape:'html'}" title="{l s='Follow my order'}"><img src="{$img_dir}icon/order.gif" alt="{l s='Follow my order'}" class="icon" /></a>
    <a href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order}&email={$email}")|escape:'html'}" title="{l s='Follow my order'}">{l s='Follow my order'}</a>
{else}
    <a href="{$link->getPageLink('history', true)|escape:'html'}" title="{l s='Back to orders'}"><img src="{$img_dir}icon/order.gif" alt="{l s='Back to orders'}" class="icon" /></a>
    <a href="{$link->getPageLink('history', true)|escape:'html'}" title="{l s='Back to orders'}">{l s='Back to orders'}</a>
{/if}
