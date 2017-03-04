<?php
namespace toolbox;
class learn_localhost_controller {

    static function setup(){

        accessControl::get()
            ->removeRequired('member');


		title::get()
		    ->addCrumb('Two Ways to Remotely Connect to Your Localhost');

    }



    function __construct(){


        widgetHelper::create()
            ->setHook('inner')
			->set('title', 'Two Ways to Remotely Connect to Your Localhost')
			->set('style', 'max-width: 620px; margin: 0 auto;')
			->set('class', 'style3')
            ->add(function($tpl){
            	/* ?>
<div class="form_panel">
<?php

messages::output('To connect to a MySQL database running on your localhost, you can use a tunnel to get around
	NAT and firewalls. There are many ways to create a localhost tunnel.

	<br><br>Two ways outlined below are
	<b>ngrok</b> (free third party software, sign up required)
	or if you already have SSH on your machine, a <b>remote SSH tunnel</b>
	(requires your own public server).', 'info', 'style4');
?>
</div>
	<div class="catchall-border style3"></div>
	<div class="catchall-border"></div> */ ?>
<div data-tabs="">
	<div style="padding: 10px;">
	<div style="max-width: 520px; margin: 0 auto;" class="switches style5">
		<div data-tabs-container="" class="container" style="border: 1px solid #ccc;">
			<span data-tab="" class="switch active">
				Reverse SSH Tunnel
			<span class="note">Requires Server</span></span>
			<span data-tab="" class="switch">
				ngrok
				<span class="note">Requires Third Party Sign-Up</span></span>
		</div>
	</div>
	</div>
	<div class="catchall-border style3"></div>
	<div class="catchall-border"></div>
	<div data-tabs-contents="" class="form_panel">

	<div data-tab-content="">
<div class="header-line left style5">
    <div class="inner">Setting a Reverse SSH Tunnel</div>
</div>
<div class="catchall spacer-1"></div>

<p><b>Step 1:</b> Open your command prompt / terminal.
	<br>(Windows users: <code class="style1"><i>Win Key</i> + <i>R</i></code>, then type
	<code class="style1"><i>cmd</i></code> and press enter)</p>
<div class="catchall spacer-3"></div>

<p><b>Step 2:</b> Enter the following command:
<div class="catchall spacer-1"></div>
	<code class="style2">ssh -R <span style="color: #FFAB1B;">3307</span>:localhost:<?php
?><span style="color: #00FF49;">3306</span> <span style="color: #FFAB1B;">root@<?php
?>example.com</span></code>
<div class="catchall spacer-3"></div>
where
	<code class="style2" style="color: #FFAB1B;">3307</code> is any open port on your remote server,
	<code class="style2" style="color: #00FF49;">3306</code> is your local MySQL port,
	and <code class="style2" style="color: #FFAB1B;">root@example.com</code> is your remote server login</p>

</p>
<div class="catchall spacer-3"></div>
<p>Once the connection is opened to your server, you should be able to
	publicly connect to your local database by using
	<code class="style1">example.com</code> on port <code class="style1">3307</code>
	and the same MySQL login as you would on your local machine.</p>


	</div>




	<div data-tab-content="" style="display: none;">
<div class="header-line left style5">
    <div class="inner">Setting up a Tunnel With ngrok</div>
</div>
<div class="catchall spacer-1"></div>

<p><b>Step 1:</b> Download ngrok for your OS:</p>
<div class="catchall spacer-1"></div>
<div class="centertext"><a target="_blank" href="https://ngrok.com/download">https://ngrok.com/download
	</a><i class="icon-link-ext"></i></div>
<div class="catchall spacer-3"></div>

<p><b>Step 2:</b> Sign up to for an account to get your authtoken: </p>
<div class="catchall spacer-1"></div>
<div class="centertext">
	<a target="_blank" href="https://dashboard.ngrok.com/auth">https://dashboard.ngrok.com/auth
		</a><i class="icon-link-ext"></i></div>
<div class="catchall spacer-3"></div>


<p><b>Step 3:</b> Install your auth token (only required once). Open your command prompt or terminal
	(Windows users: <code class="style1"><i>Win Key</i> + <i>R</i></code>, then type
	<code class="style1"><i>cmd</i></code> and press enter)
<div class="catchall spacer-1"></div>
	<code class="style2">PATH/TO/ngrok authtoken YOUR_AUTH_TOKEN</code></p>

<div class="catchall spacer-3"></div>
<p><b>Step 4:</b> Start forwarding your localhost. Replace <code class="style1">3306</code>
	with whatever port your MySQL is runs on.
<div class="catchall spacer-1"></div>
	<code class="style2">PATH/TO/ngrok tcp 3306</code></p>
<div class="catchall spacer-1"></div>
<p>ngrok will give you a url that looks something like <code class="style1"><i>tcp://0.tcp.ngrok.io:17379</i></code>,
	just copy and paste it to use on DBDiff.com!</p>


	</div>




	</div>

</div>
<?php }, 'widget.php', utils::isAjax());

        page::get()
            ->clearViews('print_messages')//we will print messages in the form area
            ->addView(
	            page::create()
	                ->addView('elements/section-centered.php')
	                ->addView(
	            function(){ ?>
	            <?php page::get()->renderViews('inner'); ?>
	            <?php }),
            'content-narrow');
    }

}
