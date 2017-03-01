import Marionette from 'backbone.marionette';
import template from '../../templates/MappChanel/itemRow.jst';
import templateEdit from '../../templates/MappChanel/itemRowEdit.jst';
export default Marionette.View.extend({
    template: template,
    isEdit: false,
    state: {
        isEditSource: false,
        isEditMedium: false,
        isEditChannel: false,
        editValue: ""
    },

    getTemplate: function () {
        console.log(this.isEdit);
        return this.isEdit ? templateEdit : template;
    },
    events: {
        "dblclick .js-source": "ondblClickSource",
        "dblclick .js-medium": "ondblClickMedium",
        "dblclick .js-channel": "ondblClickClannel",
        "change .js-edit-value": "onChangeEditValue",
        "click .js-cancel": "onCancelEdit",
        "click .js-save": "onSaveEdit"
    },
    clearEditFlags: function () {
        this.state.isEditSource = false;
        this.state.isEditMedium = false;
        this.state.isEditChannel = false;
        this.isEdit = false;
    },

    ondblClickSource: function () {
        this.clearEditFlags();
        this.isEdit = true;
        this.state.isEditSource = true;
        this.state.editValue = this.model.get("source");
        this.render();
    },
    ondblClickMedium: function () {
        this.clearEditFlags();
        this.isEdit = true;
        this.state.isEditMedium = true;
        this.state.editValue = this.model.get("medium");
        this.render();
    },
    ondblClickClannel: function () {
        this.clearEditFlags();
        this.isEdit = true;
        this.state.isEditChannel = true;
        this.state.editValue = this.model.get("channel");
        this.render();
    },
    onChangeEditValue: function (e) {
        e.preventDefault();
        this.state.editValue = e.target.value;

    },
    onCancelEdit: function () {
        this.clearEditFlags();
        this.render();
    },
    onSaveEdit: function () {
        if (this.state.isEditSource) {
            this.model.set("source", this.state.editValue);
        }
        if (this.state.isEditMedium) {
            this.model.set("medium", this.state.editValue);
        }
        if (this.state.isEditChannel) {
            this.model.set("channel", this.state.editValue);
        }
        this.model.save(this.model.changedAttributes(),{patch: true});
    }
});
