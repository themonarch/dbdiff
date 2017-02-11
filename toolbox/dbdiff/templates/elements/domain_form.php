<form id="website_input" method="GET" action="/website-report">
    <div class="inner">
       <input type="text" class="" placeholder="Enter Any Website:" maxlength="150" value="<?php 
		if(isset($domain_name)) echo $domain_name; ?>" name="website"><!--
        --><input type="submit" value="Get Stats" class="btn btn-blue btn-large go">
    </div>
</form>