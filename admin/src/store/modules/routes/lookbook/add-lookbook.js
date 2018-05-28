import lazyLoading from './lazy-loading';
import authService from '@/shared/services/auth.service';
import store from '@/store';

export default {
  name: 'AddLookbook',
  path: '/loobook/add',
  component: lazyLoading('lookbook/AddLookbook'),
  meta: {
    title: 'Add Lookbook',
    layout: 'MainLayout'
  },
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
