import axios from 'axios';
const tally = axios.create({
  baseURL: `http://e-us-east01.resetdigital.co:8080/`,
  json: true,
  headers: { 'Content-Type': 'application/json' }
});

export default tally;
