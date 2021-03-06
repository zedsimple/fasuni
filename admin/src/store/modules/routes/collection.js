import lazyLoading from './lazy-loading';
import authService from '@/shared/services/auth.service';
import store from '@/store';

export default {
  path: '/admin/collection',
  component: lazyLoading('collection/index'),
  children: [
    {
      name: 'Collection',
      path: '',
      component: lazyLoading('collection/Collection'),
      meta: {
        title: 'Collection',
        layout: 'MainLayout',
        parent: 'Collection'
      }
    },
    {
      name: 'AddCollection',
      path: 'add',
      component: lazyLoading('collection/AddCollection'),
      meta: {
        title: 'Add Collection',
        layout: 'MainLayout',
        parent: 'Collection'
      }
    }
  ],
  beforeEnter(to, from, next) {
    if (store.state.user.user) {
      next();
    } else {
      authService.index()
        .then(response => {
          if (!response || response.status === 401) {
            store.commit('auth/removeToken');
            next({name: 'Login'});
          } else {
            store.commit('user/setUser', response.data);
            next();
          }
        });
    }
  }
};
