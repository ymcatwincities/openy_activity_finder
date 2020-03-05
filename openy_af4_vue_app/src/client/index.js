import axios from 'axios'

const client = axios.create({
  baseURL: window.drupalSettings.path.baseUrl + 'af/get-data'
})

export default client
