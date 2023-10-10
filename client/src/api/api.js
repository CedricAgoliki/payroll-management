import request from '@/utils/request'

export function getAll(resource, query) {
  return request({
    url: '/' + resource,
    method: 'get'
    // params: query
  })
}

export function getResource(resource, id) {
  return request({
    url: '/' + resource + '/' + id,
    method: 'get'
    // params: { id }
  })
}

export function createResource(resource, data) {
  return request({
    url: '/' + resource,
    method: 'post',
    data
  })
}

export function updateResource(resource, data) {
  return request({
    url: '/' + resource + '/' + data.id,
    method: 'put',
    data
  })
}

export function query(route, method, data = '') {
  return request({
    url: route,
    method: method,
    data
  })
}
