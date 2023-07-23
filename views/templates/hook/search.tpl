{**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*}

<div class="block sidebar block-search">
	<div class="section">
		<div class="secondaryContent">
			<h3>{l s='Forum search' mod='xenforum'}</h3>
			<div class="pairsJustified">
				<div class="form-search">
					<form action="" method="post" id="psSearch" class="xenForm">
						<input type="text" class="inputName form-control grey" value="{$key|escape:'htmlall':'UTF-8'}" id="search" name="search" placeholder="{l s='Forum search...' mod='xenforum'}" maxlength="125" >
						<button type="submit" class="btn-inside"><span><i class="icon icon-search"></i></span></button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>