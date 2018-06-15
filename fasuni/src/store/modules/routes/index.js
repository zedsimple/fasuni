import homePage from './homepage';
import category from './category';
import product from './product';
import lookbook from './lookbook';
import lookbookArchive from './lookbookArchive';
import collection from './collection';
import storeFinder from './store-finder';
import cart from './cart';
import search from './search';
import myWhistlist from './my-whistlist';

const state = {
  homePage,
  category,
  product,
  lookbook,
  lookbookArchive,
  collection,
  storeFinder,
  cart,
  search,
  myWhistlist
};

export default {
  namespaced: true,
  state
};
