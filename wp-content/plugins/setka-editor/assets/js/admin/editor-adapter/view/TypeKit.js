var translations = window.setkaEditorAdapterL10n;

module.exports = Backbone.View.extend({

    tagName: 'link',

    attributes: {
        rel: 'stylesheet',
    },

    getSrc: function() {
        return '//use.typekit.net/' + this.model.get('id') + '.css';
    },

    getId: function() {
        return translations.names.css + '-type-kit-' + this.model.get('id');
    },

    render: function() {
        this.$el
            .attr('id',  this.getId())
            .attr('href', this.getSrc());

        document.head.appendChild(this.el);
    },
});
