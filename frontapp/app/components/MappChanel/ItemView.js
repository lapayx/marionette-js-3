import Marionette from 'backbone.marionette';
import Backbone from 'backbone';
import template from '../../templates/MappChanel/item.jst';
import templateTable from '../../templates/MappChanel/itemTable.jst';
import templatev from '../../templates/MappChanel/itemRow.jst';
import rowView from './Row.view';
import collect from "./ItemCollection";
import paginationModel from "./PagginationModel";

import templateFootee from '../../templates/MappChanel/itemFooter.jst';

const footerView =  Marionette.View.extend({
    template:templateFootee,
    events:{
        "click .js-change-page":"onChangePage"
    },
    onChangePage(e){
        e.preventDefault()
        let t = e.currentTarget.dataset.nextPage;
        t = parseInt(t);
        if (this.model.get("currentPage") == t)
            return;
        this.model.set("currentPage", parseInt(t));
        this.render();
        this.trigger("pagination:change",t);
    }
});

const collectionView =  Marionette.CollectionView.extend({

    childView: rowView,
    //this.template = templateTable;
    tagName: "tbody",
    /*collectionEvents: {
     "sync": "onSyncCollection"
     },*/
});

export default Marionette.View.extend({
    template: template,
    regions: {
        body: {
            el: 'tbody',
            replaceElement: true
        },
        footer: {
            el: '.panel-pagination',
            replaceElement: true
        }
    },
    childViewEvents: {
        'pagination:change': 'onChangePage'
    },
    modelEvents: {
        "sync": "onSyncModel"
    },
    collectionEvents: {
        "sync": "onSyncCollection"
    },
    initialize: function (paramId) {
        //GSU.loadMask.show();
        this.model = new paginationModel();
        this.model.fetch();
        this.collection = new collect();
        this.onChangePage();
        //this.collection.fetch();
    },
    onSyncCollection(){

        let c = this.collection;
        this.showChildView('body', new collectionView({collection: c}));

    },
    onSyncModel(){

        this.showChildView('footer', new footerView({model: this.model}));
    },
    onRender() {
        this.showChildView('body', new collectionView({}));
    },
    onChangePage(){
        let limit = this.model.get("pageRange");
        let offset = limit*(this.model.get("currentPage")-1);
        this.collection.fetch({data:{limit:limit, offset:offset}});
    }
});
