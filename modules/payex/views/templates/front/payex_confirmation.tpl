{capture name=path}{l s='Shipping' mod='payex'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Order summation' mod='payex'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<h3>{l s='PayEx payment' mod='payex'}</h3>

<form action="{$link->getModuleLink('payex', 'validation', [], true)|escape:'html'}" method="post">
    <input type="hidden" name="confirm" value="1" />
    <p>
        <img src="{$this_path_cod}payex.jpg" alt="{l s='Cash on delivery (COD) payment' mod='payex'}" style="float:left; margin: 0px 10px 5px 0px;" />
        {l s='You have chosen PayEx method.' mod='payex'}
        <br/><br />
        {l s='The total amount of your order is' mod='payex'}
        <span id="amount_{$currencies.0.id_currency}" class="price">{convertPrice price=$total}</span>
        {if $use_taxes == 1}
            {l s='(tax incl.)' mod='payex'}
        {/if}
    </p>
    <p>
        <br /><br />
        <br /><br />
        <b>{l s='Please confirm your order by clicking \'I confirm my order\'' mod='payex'}.</b>
    </p>
    <p class="cart_navigation" id="cart_navigation">
        <a href="{$link->getPageLink('order', true)}?step=3" class="button_large">{l s='Other payment methods' mod='payex'}</a>
        <input type="submit" value="{l s='I confirm my order' mod='payex'}" class="exclusive_large" />
    </p>
</form>
