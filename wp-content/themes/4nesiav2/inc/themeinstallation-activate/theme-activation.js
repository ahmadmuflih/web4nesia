jQuery(document).ready(function($){

	/*
	 * 4th step in theme setup
	 */

	if ( $('#activecampaign_email').val() != '' && $('#activecampaign_name').val() != '' && $('#purchase_code').val() != '' ) {
		$('#lbmn-purchase-code input[type=submit]').addClass('finishe-step');
		$('p.step-four').addClass('step-completed');
		$('.newsletter-purchase').hide();
	} else {

		// Show/Hide 4th step
		$(document).on( 'click', '.step-four-body', function(e) {
		    $('.newsletter-purchase').toggle();
		});
	}

	if ( $('#activecampaign_email').val() != '' && $('#activecampaign_name').val() != '' && $('#purchase_code').val() == '' ) {
		$('.activecampaign_form').hide();
	}

	if ( $('#activecampaign_email').val() == '' && $('#activecampaign_name').val() != '' && $('#purchase_code').val() != '' ) {
		$('#lbmn-purchase-code').hide();
	}

	if ( $('.activecampaign_form').length ) {

		/*
		 * ActiveCampaign
		 */

		window._show_thank_you = function(id, message) {
		  var form = document.getElementById('_form_' + id + '_'), thank_you = form.getElementsByClassName('_form-thank-you')[0];
		  //form.getElementsByClassName('_form-content')[0].style.display = 'none';
		  form.getElementsByClassName('_form-content')[0].classList.add("hide_form_content");
		  //form.getElementsByClassName('_form-content')[0].style.transition = '3s';
		  thank_you.innerHTML = message;
		  //thank_you.style.display = 'block';
		  thank_you.classList.add("add_thank_you");
		};
		window._show_error = function(id, message, html) {
		  var form = document.getElementById('_form_' + id + '_'), err = document.createElement('div'), button = form.getElementsByTagName('button')[0];
		  err.innerHTML = message;
		  err.className = '_error-inner _form_error _no_arrow';
		  var wrapper = document.createElement('div');
		  wrapper.className = '_form-inner';
		  wrapper.appendChild(err);
		  button.parentNode.insertBefore(wrapper, button);
		  if (html) {
		    var div = document.createElement('div');
		    div.className = '_error-html';
		    div.innerHTML = html;
		    err.appendChild(div);
		  }
		};
		window._load_script = function(url, callback) {
		    var head = document.getElementsByTagName('head')[0], script = document.createElement('script'), r = false;
		    script.type = 'text/javascript';
		    script.src = url;
		    if (callback) {
		      script.onload = script.onreadystatechange = function() {
		      if (!r && (!this.readyState || this.readyState == 'complete')) {
		        r = true;
		        callback();
		        }
		      };
		    }
		    head.appendChild(script);
		};
		(function() {
		  var getCookie = function(name) {
		    var match = document.cookie.match(new RegExp('(^|; )' + name + '=([^;]+)'));
		    return match ? match[2] : null;
		  }
		  var setCookie = function(name, value) {
		    var now = new Date();
		    var time = now.getTime();
		    var expireTime = time + 1000 * 60 * 60 * 24 * 365;
		    now.setTime(expireTime);
		    document.cookie = name + '=' + value + '; expires=' + now + ';path=/';
		  }
		      var addEvent = function(element, event, func) {
		    if (element.addEventListener) {
		      element.addEventListener(event, func);
		    } else {
		      var oldFunc = element['on' + event];
		      element['on' + event] = function() {
		        oldFunc.apply(this, arguments);
		        func.apply(this, arguments);
		      };
		    }
		  }
		  var _removed = false;
		  var form_to_submit = document.getElementById('_form_13_');
		  if ( form_to_submit !== null ) {
		  	var allInputs = form_to_submit.querySelectorAll('input, select'), tooltips = [], submitted = false;
		  } else {
		  	var allInputs = [];
		  }
		  var remove_tooltips = function() {
		    for (var i = 0; i < tooltips.length; i++) {
		      tooltips[i].tip.parentNode.removeChild(tooltips[i].tip);
		    }
		      tooltips = [];
		  };
		  var remove_tooltip = function(elem) {
		    for (var i = 0; i < tooltips.length; i++) {
		      if (tooltips[i].elem === elem) {
		        tooltips[i].tip.parentNode.removeChild(tooltips[i].tip);
		        tooltips.splice(i, 1);
		        return;
		      }
		    }
		  };
		  var create_tooltip = function(elem, text) {
		    var tooltip = document.createElement('div'), arrow = document.createElement('div'), inner = document.createElement('div'), new_tooltip = {};
		    if (elem.type != 'radio' && elem.type != 'checkbox') {
		      tooltip.className = '_error';
		      arrow.className = '_error-arrow';
		      inner.className = '_error-inner';
		      inner.innerHTML = text;
		      tooltip.appendChild(arrow);
		      tooltip.appendChild(inner);
		      elem.parentNode.appendChild(tooltip);
		    } else {
		      tooltip.className = '_error-inner _no_arrow';
		      tooltip.innerHTML = text;
		      elem.parentNode.insertBefore(tooltip, elem);
		      new_tooltip.no_arrow = true;
		    }
		    new_tooltip.tip = tooltip;
		    new_tooltip.elem = elem;
		    tooltips.push(new_tooltip);
		    return new_tooltip;
		  };
		  var resize_tooltip = function(tooltip) {
		    var rect = tooltip.elem.getBoundingClientRect();
		    var doc = document.documentElement, scrollPosition = rect.top - ((window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0));
		    if (scrollPosition < 40) {
		      tooltip.tip.className = tooltip.tip.className.replace(/ ?(_above|_below) ?/g, '') + ' _below';
		    } else {
		      tooltip.tip.className = tooltip.tip.className.replace(/ ?(_above|_below) ?/g, '') + ' _above';
		    }
		  };
		  var resize_tooltips = function() {
		    if (_removed) return;
		    for (var i = 0; i < tooltips.length; i++) {
		      if (!tooltips[i].no_arrow) resize_tooltip(tooltips[i]);
		    }
		  };
		  var validate_field = function(elem, remove) {
		    var tooltip = null, value = elem.value, no_error = true;
		    remove ? remove_tooltip(elem) : false;
		    if (elem.type != 'checkbox') elem.className = elem.className.replace(/ ?_has_error ?/g, '');
		    if (elem.getAttribute('required') !== null) {
		      if (elem.type == 'radio' || (elem.type == 'checkbox' && /any/.test(elem.className))) {
		        var elems = form_to_submit.elements[elem.name];
		        no_error = false;
		        for (var i = 0; i < elems.length; i++) {
		          if (elems[i].checked) no_error = true;
		        }
		        if (!no_error) {
		          tooltip = create_tooltip(elem, "Please select an option.");
		        }
		      } else if (elem.type =='checkbox') {
		        var elems = form_to_submit.elements[elem.name], found = false, err = [];
		        no_error = true;
		        for (var i = 0; i < elems.length; i++) {
		          if (elems[i].getAttribute('required') === null) continue;
		          if (!found && elems[i] !== elem) return true;
		          found = true;
		          elems[i].className = elems[i].className.replace(/ ?_has_error ?/g, '');
		          if (!elems[i].checked) {
		            no_error = false;
		            elems[i].className = elems[i].className + ' _has_error';
		            err.push("Checking %s is required".replace("%s", elems[i].value));
		          }
		        }
		        if (!no_error) {
		          tooltip = create_tooltip(elem, err.join('<br/>'));
		        }
		      } else if (elem.tagName == 'SELECT') {
		        var selected = false;
		        for (var i = 0; i < elem.options.length; i++) {
		          if (elem.options[i].selected) {
		            selected = true;
		            break;
		          }
		        }
		        if (!selected) {
		          no_error = false;
		          tooltip = create_tooltip(elem, "Please select an option.");
		        }
		      } else if (value === undefined || value === null || value === '') {
		        elem.className = elem.className + ' _has_error';
		        no_error = false;
		        tooltip = create_tooltip(elem, "This field is required.");
		      }
		    }
		    if (no_error && elem.name == 'email') {
		      if (!value.match(/^[\+_a-z0-9-'&=]+(\.[\+_a-z0-9-']+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i)) {
		        elem.className = elem.className + ' _has_error';
		        no_error = false;
		        tooltip = create_tooltip(elem, "Enter a valid email address.");
		      }
		    }
		    if (no_error && /date_field/.test(elem.className)) {
		      if (!value.match(/^\d\d\d\d-\d\d-\d\d$/)) {
		        elem.className = elem.className + ' _has_error';
		        no_error = false;
		        tooltip = create_tooltip(elem, "Enter a valid date.");
		      }
		    }
		    tooltip ? resize_tooltip(tooltip) : false;
		    return no_error;
		  };
		  var needs_validate = function(el) {
		    return el.name == 'email' || el.getAttribute('required') !== null || (el.className ? /date_field/.test(el.className) : false);
		  };
		  var validate_form = function(e) {
		    var err = form_to_submit.getElementsByClassName('_form_error')[0], no_error = true;
		    err ? err.parentNode.removeChild(err) : false;
		    if (!submitted) {
		      submitted = true;
		      for (var i = 0, len = allInputs.length; i < len; i++) {
		        var input = allInputs[i];
		        if (needs_validate(input)) {
		          if (input.type == 'text') {
		            addEvent(input, 'input', function() {
		              validate_field(this, true);
		            });
		          } else if (input.type == 'radio' || input.type == 'checkbox') {
		            (function(el) {
		              var radios = form_to_submit.elements[el.name];
		              for (var i = 0; i < radios.length; i++) {
		                addEvent(radios[i], 'click', function() {
		                  validate_field(el, true);
		                });
		              }
		            })(input);
		          } else if (input.tagName == 'SELECT') {
		            addEvent(input, 'change', function() {
		              validate_field(input, true);
		            });
		          }
		        }
		      }
		    }
		    remove_tooltips();
		    for (var i = 0, len = allInputs.length; i < len; i++) {
		      var elem = allInputs[i];
		      if (needs_validate(elem)) {
		        validate_field(elem) ? true : no_error = false;
		      }
		    }
		    if (!no_error && e) {
		      e.preventDefault();
		    }
		    resize_tooltips();
		    return no_error;
		  };
		  addEvent(window, 'resize', resize_tooltips);
		  addEvent(window, 'scroll', resize_tooltips);
		  var form_submit = function(e) {
		    e.preventDefault();
		    if (validate_form()) {
		            var serialized = serialize(document.getElementById('_form_13_'));
		      _load_script('https://lumbermandesigns.activehosted.com/proc.php?' + serialized + '&jsonp=true');
		    }
		    return false;
		  };
		  addEvent(form_to_submit, 'submit', form_submit);
		  _load_script("//d3rxaij56vjege.cloudfront.net/form-serialize/0.3/serialize.min.js");
		})();

		$('.activecampaign_form').submit(function(){

			//$('._form-content').fadeOut('slow');

			var email = $('#activecampaign_email').val();
			var name = $('#activecampaign_name').val();

			$.ajax({
				type: "POST",
				data: {
					email: email,
					name: name,
					security: lbmnajax,
					action: 'lbmn_activecampaign_email'
				},
				url: ajaxurl,
			});
		});

   } // if $('.activecampaign_form').length


	/*
	 * Purchase code
	 */

	if ( $('#purchase_code').val() == '' ) {
		$('#lbmn-purchase-code > #step_loader_res').css({'display' : 'none'});
	} else {
		$('#lbmn-purchase-code > #step_loader_res').css({'display' : 'block'});
	}

	$('#lbmn-purchase-code').submit(function(){

		var purchase_code = $('#purchase_code').val();

		if ( validateCode(purchase_code) ) {
			$.ajax({
				type: "POST",
				data: {
					formData: purchase_code,
					security: lbmnajax,
					action: 'lbmn_code_action'
				},
				url: ajaxurl,
				beforeSend: function() {
					$('#lbmn-purchase-code > #step_loader_res').fadeOut(300, function() {
						$('#lbmn-purchase-code > #step_loader').fadeIn(400);
					});
					$('#lbmn-purchase-code > .error').fadeOut();
				},
				success: function(res){
					$('#lbmn-purchase-code > #step_loader').fadeOut(300, function() {
						$('#lbmn-purchase-code > #step_loader_res').fadeIn(500);
						if ( ( $('#activecampaign_email').val() != '' && $('#activecampaign_name').val() != '' ) || jQuery(".add_thank_you").length ) {
							$('#lbmn-purchase-code input[type=submit]').addClass('finishe-step');
							$('.newsletter-purchase').fadeOut(600, function() {
								$('p.step-four').addClass('step-completed');
							});
						}
					});
				},
				error: function(){
					$('#lbmn-purchase-code > .error').fadeOut(500);
					$('#lbmn-purchase-code > #step_loader').fadeIn(500, function() {
						$('#lbmn-purchase-code > #step_loader').fadeOut(600);
						$('#lbmn-purchase-code > .error').fadeIn(600, function() {
							$('#lbmn-purchase-code > .error').text("Error 404");
						});
					});
				}
			});
			return false;
		} else {
			$('#lbmn-purchase-code > .error').fadeOut(500);
			$('#lbmn-purchase-code > #step_loader').fadeIn(500, function() {
				$('#lbmn-purchase-code > #step_loader').fadeOut(600);
				$('#lbmn-purchase-code > .error').fadeIn(600, function() {
					$('#lbmn-purchase-code > .error').text("Code is invalid");
				});
			});

			return false;
		}
	});

	function validateCode(code) {
	    var re = /([a-z|\d|-]+)/;
	    return re.test(code);
	}

});
