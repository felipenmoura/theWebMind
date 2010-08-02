/*
 * jQuery UI wizard plug-in 0.9.5
 *
 *
 * Copyright (c) 2009 Jan Sundman (jan.sundman[at]aland.net)
 * Copyright (c) 2009 James M. Curran (jamescurran[at]mvps.org)
 
 * Licensed under the MIT licens:
 *   http://www.opensource.org/licenses/mit-license.php
 *   
 *
 * Changelog:
 * version 0.9.5a
 *  Fork of Jan's original by James.   Principal changes:
 *    - Changed API to confirm to jQuery UI standards.  Notably, it's
 *      now called wizard() instead of formwizard(). (a formwizard()
 *      function remains which adapts the old-style calls to the new API)
 *   - Added jQuery UI-ish CSS classes for styling.
 *   - Refactored some code.
 *   - added three new options:
 *    o   validationOptions:  A set of key/value pairs to set as configuration 
 *                properties for the validation plugin. (formerly a separate 
 *                parameter in formwizard)
 *    o   formOptions: A set of key/value pairs to set as configuration 
 *                properties for the form plugin. (formerly a separate parameter 
 *                in formwizard)
 *    o  autoDisableNext:  When set to true, the "Next" button is automatically 
 *                disabled, requiring it be manually enable once the user has 
 *                completed the step. 
 *   - added five new methods
 *    o enableNext() - 
 *    o disableNext() - 
 *    o enableBack() - 
 *    o disableBack() - 
 *    o gotoStep(step) - navigates to the given step.  step can be either a 0-based index, or a jQuery selector.
 *  - added one new callback
 *     o show() - called each time a new panel is displayed.          
 *         function show(evnt, args)
 *              event - standard jQuery UI event object.
 *              args - object with fields:  
 *                 step - jQuery wrapped object of the DIV being displayed.
 *                 stepInx - 0-based index of the DIV.
 *                 backwards - true if reached by clicking "back", false otherwise.
 *      (callback afterNext & afterBack remain, but are depreciated)
 * 
 * version 0.9.5
 * -------------
 * - Fix for enabling optional validation
 *
 * version 0.9.4
 * -------------
 * - Performance fixes for validation of the steps
 * - Performance fixes for rendering of the steps
 * - Introduces a need for input fields in the wizard to be disabled in the html
 *
 * version 0.9.3
 * ------------- 
 * - Fixed the continueToNextStep and backButton.click callback to handle navigation correctly when the 
 * history plugin is not used
 * 
 * version 0.9.2 
 * - A check was added to see if there are multiple links on one step. In the
 * case there are we assume they are radio buttons or checkboxes. Only the
 * one that is checked is considered a valid link. This fixes a bug where links
 * in the form of radio buttons do not work. Credits to adnanshareef for 
 * reporting the bug.  
 * 
 * - Added initial functionality for doing server-side validation 
 * 
 * version 0.9.1 
 * -------------
 * - Addition of afterNext and afterBack callbacks, can be used to do stuff after
 * the rendering of a step has been completed
 * 
 * version 0.9.0 
 * -------------
 * - Initial release
 *
 */

(function($)
{

    $.widget("ui.wizard",
    {
        _init: function()
        {			
            if (typeof (this.options.formPluginEnabled) == "undefined")
                this.options.formPluginEnabled = (this.options.formOptions == null);
            if (this.options.formPluginEnabled)
            {
                this.options.formOptions = $.extend({ reset: true, success: function(data) { alert("success"); } }, this.options.formOptions);
                var formOptionsSuccess = this.options.formOptions.success;
                var formSettings = $.extend(this.options.formOptions, { success: function(data)
                {
                    if (formOptions.resetForm)
                    {
                        _navigate(0);
                        if (this.options.historyEnabled)
                        {
                            $.historyLoad(0);
                        }
                        else
                        {
                            _renderStep();
                        }
                    }
                    formOptionsSuccess(data);
                }
                });
            }
            else
            {
                if (typeof (this.options.formOptions) == "undefined")
                    this.options.formOptions = { success: function() { } };
                else
                    this.options.formOptions.success = function() { };
            }
            /**
            * Initialization
            */
            // if historyEnabled not explicit defined, set it based on presence/absence of jquery.history.js plugin


            if (this.options.historyEnabled == undefined)
                this.options.historyEnabled = ($.historyInit != undefined);

            this.element.addClass("ui-wizard ui-widget ui-helper-reset");

            this.steps = this.element.find(".step");
            this.steps.addClass("ui-wizard-content ui-helper-reset ui-widget-content ui-corner-all")

            this.currentStep = 0;
            this.previousStep = undefined;
            this.backButton = this.element.find(this.options.back);
            this.nextButton = this.element.find(this.options.next);

            this.backButton.addClass("ui-wizard-content ui-helper-reset ui-state-default ui-state-hover  ui-state-active");
            this.nextButton.addClass("ui-wizard-content ui-helper-reset ui-state-default ui-state-hover  ui-state-active");

            this.activatedSteps = new Array();
            this.isLastStep = false;
            var wizard = this;         // for closures in the next lines.

            if (this.options.historyEnabled)
            {
                if ($.historyInit == undefined)
                {
                    this.options.historyEnabled = false;
                    alert("the history plugin needs to be included");
                }
                else
                {
                    location.hash = "";
                    $.historyInit(function(hash) { wizard._handleHistory(hash); });
                }
            }
            else
            {
                this._handleHistory(0);
            }

            if (this.options.validationEnabled == undefined)
                this.options.validationEnabled = (this.options.validationSettings == {});

            if (jQuery().validate == undefined)
            {
                if (this.options.validationEnabled)
                {
                    this.options.validationEnabled = false;
                    alert("the validation plugin needs to be included");
                }
            }
            else if (this.options.validationEnabled)
            {
                this.element.validate(this.options.validationSettings);
            }

            if (this.options.formPluginEnabled && jQuery().ajaxSubmit == undefined)
            {
                this.options.formPluginEnabled = false;
                alert("the form plugin needs to be included");
            }

            /** 
            * Navigation event callbacks 
            */
            var wizard = this;         // for closures in the next lines.
            this.nextButton.click(function() { return wizard._nextButton_click(this); });
            this.backButton.click(function() { return wizard._backButton_click(this); });
            this.backButton.val(this.options.textBack).text(this.options.textBack);
            $("input", this.element).attr("disabled", "disabled");
            this.steps.hide();
            this._renderStep();
            return $(this);
        },

        destroy: function()
        {
            this.element.removeClass("ui-formwizard ui-widget ui-helper-reset");
            this.steps.removeClass("ui-wizard-content ui-helper-reset ui-widget-content ui-corner-all")
            this.backButton.removeClass("ui-wizard-content ui-helper-reset ui-state-default ui-state-hover  ui-state-active  ui-state-disabled")
                .unbind("click");
            this.nextButton.removeClass("ui-wizard-content ui-helper-reset ui-state-default ui-state-hover  ui-state-active  ui-state-disabled")
                .unbind("click");
        },

        /* 
        ***********************************
        */
        activate: function(step)
        {
            gotoStep(step);
        },
        /* 
        ***********************************
        */
        _backButton_click: function(btn)
        {
            if (this.activatedSteps.length > 0)
            {
                if (this.options.historyEnabled)
                {
                    history.back();
                }
                else
                {
                    this._handleHistory(this.activatedSteps[this.activatedSteps.length - 2]);
                }
            }

            this._trigger("afterBack");

            return false;
        },
        /**
        * Checks if the step is the last step in a wizard route
        *
        * @name checkIflastStep
        * @type undefined
        * @param Number step The step to check.
        */
        _checkIflastStep: function(step)
        {
            var link = this._getLink($(this.steps[step]));

            this.isLastStep = false;

            if ((("." + link) == this.options.submitStepClass) || (link == undefined && (step * 1) == this.steps.length - 1))
            {
                this.isLastStep = true;
            }
        },

        /**
        * Continues to the next step in the wizard
        */
        _continueToNextStep: function()
        {
            this._navigate(this.currentStep);
            this._renderStep();

            if (this.options.historyEnabled)
            {
                $.historyLoad(this.currentStep);
            }
            else
            {
                this._handleHistory(this.currentStep);
            }

            this._trigger("afterNext");
        },

        /* 
        ***********************************
        */
        disableBack: function()
        {
            this.backButton.attr('disabled', 'disabled').addClass("ui-state-disabled");
        },

        /* 
        ***********************************
        */
        disableNext: function()
        {
            this.nextButton.attr('disabled', 'disabled').addClass("ui-state-disabled");
        },

        /* 
        ***********************************
        */
        enableBack: function()
        {
            this.backButton.removeAttr("disabled").removeClass("ui-state-disabled");
        },
        /* 
        ***********************************
        */
        enableNext: function()
        {
            this.nextButton.removeAttr("disabled").removeClass("ui-state-disabled");
        },

        /**
        * Finds the valid link for the step (if there is one)
        *
        * @name getLink
        * @type String
        * @param Number step The step to search for valid links
        */
        _getLink: function(step)
        {
            var link = undefined;
            var links = step.find(this.options.linkClass);

            if (links.length == 1)
            {
                link = links.val();
            }
            else if (links.length > 1)
            {
                // assume that the link is a radio button or checkbox
                link = step.find(this.options.linkClass + ":checked").val();
            }

            return link;
        },

        gotoStep: function(step)
        {
            var stepInx;
            if (typeof (step) == "string")
                stepInx = this.steps.index($(step));
            else
                stepInx = step;

            // If in range, go there.  Otherwise, silently exit.
            if (stepInx >= 0 && stepInx < this.steps.length)
                this._handleHistory(stepInx);
        },

        /**
        * Handles back navigation (and browser back and forward buttons if history is enabled)
        *
        * @name handleHistory
        * @type undefined
        * @param String hash The hash used in the browser history
        
        If hash ==previous step, remove current step from activatedSteps, and then goto previous.
        Otherwise, add hash to end of activatedSteps, and go there.
        */
        _handleHistory: function(hash)
        {
            var direction = false;
            if (!hash)
            {
                hash = 0;
            }
            if (this.activatedSteps[this.activatedSteps.length - 2] == hash)
            {
                this.activatedSteps.pop();
                direction = true;
            }
            else
            {
                this.activatedSteps.push(hash);
            }
            this.previousStep = this.currentStep;
            this.currentStep = hash;
            this._checkIflastStep(hash);
            this._renderStep();

            if (this.options.autoNextDisabled)
                this.disableNext();
            this._trigger("show", null, { step: $(this.steps[this.currentStep]), stepInx: this.currentStep, backward: direction });

        },
        /**
        * Decides and sets the current step in the wizard 
        *
        * @name navigate
        * @type undefined
        * @param Number step The step to navigate from.
        */
        _navigate: function(stepInx)
        {
            var step = $(this.steps[stepInx]);
            var link = this._getLink(step);

            if (link)
            {
                var navigationTarget = this.steps.index($("#" + link));
                if (navigationTarget.length == 0)
                {
                    return;
                }
                else
                {
                    this.previousStep = this.currentStep;
                    this.currentStep = navigationTarget;
                }

                this._checkIflastStep(step);
            }
            else if (!this.isLastStep)
            {
                this.previousStep = this.currentStep;
                this.currentStep++
                this._checkIflastStep(this.currentStep);
            }
        },
        /* 
        ***********************************
        */
        _nextButton_click: function(btn)
        {
            if (this.options.validationEnabled)
            {
                var valid = true;
                var form = this.element;
                $.each(form.find("input:enabled, select:enabled"), function()
                {
                    if (form.validate().element($(this)) == false)
                        valid = false;
                })
                if (!valid) return false;
            }

            if (this.isLastStep)
            {
                if (this.options.formPluginEnabled)
                {					
                    this.element.ajaxSubmit(this.options.formSettings);
                    return false;
                }
				
				/*
					Editado para suportar executar outra ação ao invés de submeter o formulário
					this.element.submit();
				*/
                
				this.options.onSubmit();
                return false;
            }

            // Doing server side validation for the steps
            if (this.options.serverSideValidationUrls)
            {
                var url = "";
                var errorCallback = undefined;
                $.each(this.options.serverSideValidationUrls, function()
                {
                    if (this.validation.step == this.currentStep)
                    {
                        url = this.validation.url;
                        errorCallback = this.validation.error;
                    }
                });

                if (url != "")
                {
                    this.element.ajaxSubmit(
                            { url: url,
                                success: function() { this._continueToNextStep(); },
                                error: function() { errorCallback(); }
                            });
                    alert("server side done");
                    return false;
                }
            }
            this._continueToNextStep();
            return false;
        },

        /**
        * Renders the current step and disables the input fields in other steps
        *
        * @name renderStep
        * @type undefined
        */
        _renderStep: function()
        {
            this.enableBack();
            this.nextButton.val(this.options.textNext).text(this.options.textNext);
            //            var steps = this._getData('steps');

            if (this.previousStep != undefined)
            {
                this.steps.eq(this.previousStep).hide()
					.find("input")
					.attr("disabled", "disabled");
            }
			var effect = this.options.animated;
            this.steps.eq(this.currentStep)[effect]()
			   .find("input").removeAttr("disabled");

            if (this.isLastStep)
            {
                for (var i = 0; i < this.activatedSteps.length; i++)
                {
                    this.steps.eq(this.activatedSteps[i]).find("input").removeAttr("disabled");
                }
                this.nextButton.val(this.options.textSubmit).text(this.options.textSubmit);
            }
            else if (this.currentStep == 0)
            {
                this.disableBack();
            }
        }
    });

    $.extend($.ui.wizard, {
        version: "@VERSION",
        defaults:
        {
	        animated: 'fadeIn',
            historyEnabled: undefined,
            validationEnabled: undefined,
            formPluginEnabled: undefined,
            linkClass: ".link",
            submitStepClass: ".submit_step",
            back: ".wizard_back",
            next: ".wizard_next",
            textSubmit: 'Finish',
            textNext: 'Next',
            textBack: 'Back',
            afterNext: undefined,
            afterBack: undefined,
            show: undefined,
            autoNextDisabled: true,
            serverSideValidationUrls: undefined,
            formOptions: undefined,
            validationOptions: {},
			/*
				Revision: Equipe TheWebMind
				Data: 30/08/2009 
				Implementado método onSubmit para suportar receber uma função ao invés de submeter o formulário				
			*/
			onSubmit : function(){},
        }
    });

    /**
    * Creates a wizard of all matched elements
    *
    * @constructor
    * @name $.formwizard
    * @param Hash wizardSettings A set of key/value pairs to set as configuration properties for the wizard plugin.
    * @param Hash validationSettings A set of key/value pairs to set as configuration properties for the validation plugin.
    * @param Hash formOptions A set of key/value pairs to set as configuration properties for the form plugin.
    */

    $.fn.formwizard = function(wizardSettings, validationSettings, formOptions)
    {
        return this.formwizard($.extend(wizardSettings, { back: ":reset", next: ":submit", autoNextDisabled: false, validationOptions: validationSettings, formOptions: formOptions }));
    }
}
)(jQuery);