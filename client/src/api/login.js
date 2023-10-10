import request from '@/utils/request'

export function loginByUsername(username, password) {
  const data = {
    "email": username,
    "password": password
  }
  return request({
    // url: '/login/login',
    url: '/auth/login',
    method: 'post',
    data
  })
}

export function logout(token) {
  return request({
    url: '/auth/logout?token='+token,
    method: 'post'
  })
}

export function getUserInfo(token) {
  return request({
    url: '/auth/me?token='+token,
    //url: '/login/info',
    method: 'post',
    //params: { token }
  })
}

