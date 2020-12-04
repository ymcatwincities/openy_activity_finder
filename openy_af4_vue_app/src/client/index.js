import axios from 'axios'

const DEFAULT = 'af/get-data'
const SESSION_DATA = 'af/api/v1/session-data'

const client = flag => {
  let url = ''
  switch (flag) {
    case 'session_data':
      url = SESSION_DATA
      break

    default:
      url = DEFAULT
  }
  return axios.create({
    baseURL: window.drupalSettings.path.baseUrl + url
  })
}

export default client
