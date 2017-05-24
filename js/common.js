	function pushMessage(m,t,i){
		// m = Message, t = Title of notification, i = Image / Icon (full path)
		Push.create('MRICF: '+t, {
			body: m,
			icon: i,
			timeout: 25000,
			onClick: function () {
				console.log("Fired!");
				window.focus();
				this.close();
			},
			vibrate: [200, 100, 200, 100, 200, 100, 200]
		});
	}

