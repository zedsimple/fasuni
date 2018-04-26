import httpClient from './../http';
import { handleError } from './../functions';
import { AUTH } from './../constants/api';

export default {
  index() {
    return httpClient.get(AUTH.index)
      .catch(handleError);
  },
  login(body) {
    return httpClient.post(AUTH.login, body)
      .catch(handleError);
  },
  logout() {
    return httpClient.get(AUTH.logout)
      .catch(handleError);
  },
  register(body) {
    return httpClient.post(AUTH.register, body)
      .catch(handleError);
  }
};
