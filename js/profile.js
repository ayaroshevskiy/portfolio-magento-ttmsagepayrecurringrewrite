var FrequencyCycle = Class.create();
FrequencyCycle.prototype = {
    initialize: function(frequencyRowClass, editFormId){
        this.frequencyRow = $(frequencyRowClass);
        this.frequencyChangeButton = this.frequencyRow.select('button.change')[0];
        this.editForm = $(editFormId);
        this.editFormCancelButton = this.editForm.select('button.cancel')[0];

        this.editForm.hide();
        Event.observe(this.frequencyChangeButton, 'click', this._onFrequencyChangeClick.bindAsEventListener(this));
        Event.observe(this.editFormCancelButton, 'click', this._onEditFormCancelClick.bindAsEventListener(this));
    },

    _onFrequencyChangeClick: function(event) {
        this.frequencyRow.hide();
        this.editForm.show();
    },

    _onEditFormCancelClick: function(event) {
        this.editForm.hide();
        this.frequencyRow.show();
    }
}