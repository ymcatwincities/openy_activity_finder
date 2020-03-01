import axios from 'axios'

const client = axios.create({
  baseURL: 'af/get-data'
})

export default client
