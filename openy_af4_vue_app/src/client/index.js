import axios from 'axios'

const DEFAULT = 'af/get-data'
const REST = 'af/api/v1/session_data'

const client = flag => {
  let url = ''
  switch (flag) {
    case 'rest':
      url = REST
      break

    default:
      url = DEFAULT
  }
  return axios.create({
    baseURL: window.drupalSettings.path.baseUrl + url
  })
}

export default client
