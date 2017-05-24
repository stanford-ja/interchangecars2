	function pushMessage(m,t){
		Push.create('MRICF: '+t, {
			body: m,
			icon: 'icon.png',
			timeout: 25000,
			onClick: function () {
				console.log("Fired!");
				window.focus();
				this.close();
			},
			vibrate: [200, 100, 200, 100, 200, 100, 200]
		});
	}

