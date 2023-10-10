import request from '@/utils/request'

export function getAll(query) {
  return request({
    url: '/fonction',
    method: 'get'
    // params: query
  })
}

export function getFonction(id) {
  return request({
    url: '/fonction/' + id,
    method: 'get'
    // params: { id }
  })
}

export function createFonction(data) {
  return request({
    url: '/fonction',
    method: 'post',
    data
  })
}

export function updateFonction(data) {
  return request({
    url: '/fonction/' + data.id,
    method: 'put',
    data
  })
}
