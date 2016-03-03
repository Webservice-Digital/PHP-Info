	(function (d) {

		// micro jQ
		window.$ = function(selector) {
			return d[{
				'#': 'getElementById',
				'.': 'getElementsByClassName',
				'@': 'getElementsByName',
				'=': 'getElementsByTagName'
			}[ selector[0] ] 
			|| 'querySelectorAll'](selector)
		};
		// micro loDash
		function each(list, fn) {
			Array.prototype.forEach.call(list,fn);
		}
		function text(elm) {
			return elm.innerText || elm.textContent;
		}

		/*****************/

		// lets colors to be more sandy smoothy, and remember this
		var storeKeyName = 'phpInfoGold';
		var themeClassName = 'golden';
		if (localStorage.getItem(storeKeyName)==='true')
			d.body.classList.add(themeClassName);
		// lets tab icon changes too
		var docIcon = d.getElementById('docIcon');
		function setIcon() {
			docIcon.href = window.getComputedStyle(d.getElementsByClassName('php-logo')[0],null).getPropertyValue('background-image').match(/url\(("?)(.+)\1\)/)[2];
		}
		setIcon();
		// lets take control on it!
		d.getElementById('gold').addEventListener('click',
			function(e){
				d.body.classList.toggle(themeClassName);	
				localStorage.setItem(storeKeyName,d.body.classList.contains('golden'));	
				setIcon();	
			} );
		// lets long values stay more readable
		// 1. get the cells with vals
		var tds = $('td:nth-child(2)');
		var sre = (text(tds[0]).match(/^\w+/)[0] === 'Windows') ? /[;,]/g : /[:,]/g;
		// 2. lets do that - iterate throughout bunch of cells with values
		each(tds, function(val, idx) {
		  // tds[idx].innerHTML = val.innerText.split(sre).join('<br>')
		  tds[idx].innerHTML = text(val).replace(sre, '$& ');
		});
		// filter for navigation
		// for older browsers change `input` event trigger to `keyup`,
		// wich messy, but will work. Disatvantages of `keyup` in
		// false firing on `alt`, `shift` and so on, but not fired on
		// clear or paste text with mouse. `change` trigger can be tracked,
		// but it fires only when input box loose the focus, so not informant
		var navs = $(' .phpinfo-nav li');
		var secs = $(' .phpinfo-section');
		d.getElementById('filterText').addEventListener('input',
			function(e){
				var re = new RegExp(e.target.value,'i');
				each(navs, function (val,idx) {
					if (text(val).search(re) < 0) {
						val.classList.add('hide');
						secs[idx].classList.add('hide');
					}
					else {
						val.classList.remove('hide');
						secs[idx].classList.remove('hide');
					}
				} );
			} );
		d.getElementsByClassName('filterForm')[0].addEventListener('reset', function (e) {
			each(navs, function (val, idx) {
				val.classList.remove('hide');
				secs[idx].classList.remove('hide');
			});
		});
		d.body.addEventListener('keyup', function (e) {
			if (e.code === 'Escape') 
				d.getElementsByClassName('filterForm')[0].reset();
			// if (e.keyCode === 27) d.getElementsByClassName('filterForm')[0].reset();
		})
	})(document);
