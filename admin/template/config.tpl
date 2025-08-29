{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

<div class="titlePage">
    <h2>Okta SAML</h2>
</div>

<form method="post" class="properties" style="margin-bottom:60px;">
  <fieldset class="mainConf">

    <legend><span class="icon-wrench icon-purple"></span>{'Identity provider'|translate}</legend>
    
    <ul>
      <li>
        <label>{'IDP Entity ID'|translate}</label><br>
        <input type="text" name="idp_entity_id" value="{if isset($OKTA_CONNECT)}{$OKTA_CONNECT['idp_entity_id']}{/if}" style="width:100%;"><br><br>
      </li>

      <label>{'SSO URL'|translate}</label><br>
      <input type="text" name="sso_url" value="{if isset($OKTA_CONNECT)}{$OKTA_CONNECT['sso_url']}{/if}" style="width:100%;"><br><br>

      <label>{'X.509 Certificate'|translate}</label><br>
      <textarea name="x509cert" rows="10" style="width:100%;">{if isset($OKTA_CONNECT)}{$OKTA_CONNECT['x509cert']}{/if}</textarea><br><br>
    </ul>

    <legend><span class="icon-cog icon-blue"></span>{'Other'|translate}</legend>

    <ul>
      <label>{'Admin role name in okta'|translate}</label><span class="icon-help-circled tiptip" style="cursor:help" title="Copy and paste the role configured in Okta for admin users "></span><br>
      <input type="text" name="admin_role_name" value="{if isset($OKTA_CONNECT)}{$OKTA_CONNECT['admin_role_name']}{/if}" style="width:100%;"><br><br>

      <label>{'User role name in okta'|translate}</label><span class="icon-help-circled tiptip" style="cursor:help" title="Copy and paste the role configured in Okta for normal users "></span><br>
      <input type="text" name="user_role_name" value="{if isset($OKTA_CONNECT)}{$OKTA_CONNECT['user_role_name']}{/if}" style="width:100%;"><br><br>

      <span>{'If no roles are configured users will be have guest status'|translate}</span>
      <br>
      <br>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="altaccess" {if ($OKTA_CONNECT['altaccess'])}checked="checked"{/if}>
          {'Keep alternate access to the standard Piwigo identification page'|translate}
        </label>
      </li>

    </ul>

  <legend><span class="icon-info-circled-1 icon-green"></span>{'How to configure okta'|translate}</legend>
  <ul>
    <li>
      <span>{'In okta you must configure the Service Provider entityId as <i>"https://yourPiwigoURL/piwigo/plugins/okta_connect/metadata.php"</i>'|translate}</span><br>
      <span>{'The service provider Assertion Consumer Service should be <i>"https://yourPiwigoURL/piwigo/identification.php?okta_sso=tryLoginPiwigo"</i>'|translate}</span>
    </li>
  </ul>


  </fieldset>


  <!-- Savebar -->
    <div class="savebar-footer">
      <div class="savebar-footer-start">
      </div>
      <div class="savebar-footer-end">

      {if isset($save_success)}
        <div class="savebar-footer-block">
          <div class="badge info-message">
            <i class="icon-ok"></i>{$save_success}
          </div>
        </div>
      {/if}

        <div class="savebar-footer-block">
          <button class="buttonLike" type="submit" name="save_okta_connect"><i class="icon-floppy"></i> {'Save settings'|translate}</button>
        </div>
      </div>
    </div>

</form>
