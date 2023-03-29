import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'

export const useAppStore = defineStore('app', {
  state: () => ({
    netto: 0,
    brutto: 0,
    gps: 1,
    years: 0,
    year: 0,
    activeResult: 'price',
    price: {
      errors: {
        year: false,
        price: false,
        request: false
      },
      loading: false,
      calculated: 0
    },
    installment: {
      errors: {
        request: false
      },
      loading: false,
      calculated: 0,
      years: 0
    }
  }),

  actions: {
    setItem(item, value){
      this[item] = value
    },
    setActiveResult(active){
      this.activeResult = active
    },
    setItemError(item, key, error){
      this[item].errors[key] = error
    },
    setErrorsDefault(type){
      if(type == 'price'){
        this.price.errors = {
          year: false,
          price: false,
          request: false
        }
        return
      }

      this.installment.errors = {
        request: false
      }
    },
    getItemPrice(){
      this.price.errors.request = false
      this.price.loading = true
      this.setErrorsDefault('price')

      let payload = {
        'price': this.netto,
        'year': this.year,
        'gps': this.gps
      }

      return fetch('http://' + window.location.hostname + '/api/calculator/price', {
        method: "POST",
        headers: { 'Content-type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(res=>res.json())
      .then((response) => {
        this.price.loading = false
        this.price.calculated = response.data.price
        this.setActiveResult('price')

        return Promise.resolve(response)
      }).catch((error) => {
        this.price.errors.request  = true
        this.price.loading = false

        return Promise.reject(error)
      });
    },
    getItemPriceWithInstallments(){
      if(this.years == 0){
        this.installment.calculated = this.price.calculated
        this.installment.years = 0
        this.setActiveResult('installments')

        return
      }

      this.installment.errors.request = false
      this.installment.loading = true
      this.setErrorsDefault('installments')

      let payload = {
        'price': this.price.calculated,
        'years': this.years
      }

      return fetch('http://' + window.location.hostname + '/api/calculator/installment', {
        method: "POST",
        headers: { 'Content-type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(res=>res.json())
      .then((response) => {
        this.installment.loading = false
        this.installment.calculated = response.data.price
        this.installment.years = this.years
        this.setActiveResult('installments')

        return Promise.resolve(response)
      }).catch((error) => {
        this.installment.errors.request = true
        this.installment.loading = false

        return Promise.reject(error)
      });
    }
  }
})
