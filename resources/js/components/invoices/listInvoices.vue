<template>
    <div>
    <v-data-table :headers="headers" :items="invoice" item-key="id" :pagination.sync="pagination" :total-items="totalInvoices" :rows-per-page-items="pagination.rowsPerPageItems"   >
      <template slot="items" slot-scope="props">  
        <td >{{ props.item.meals.table_number }}</td>
        <td class="text-xs-left">{{ props.item.state }}</td>
        <td class="text-xs-left">{{ props.item.meals.users.name  }}</td>
        <td class="text-xs-left">{{ props.item.meals.total_price_preview }}</td>
        <div v-if="typeInvoice == 'filingInvoice'">
            <v-btn round color="primary" v-if="!props.expanded && emptyCheker(props.item) && updatedInvoice == 0"  style="float: left;" dark primary hide-details @click="props.expanded = !props.expanded"> Concluir  &emsp; <v-icon dark >edit</v-icon></v-btn>
            <v-btn round color="amber" v-else-if="!props.expanded && pendingCheker && emptyCheker(props.item) == false"  style="float: left;" dark primary hide-details @click="props.item.state = 'paid'; sendInvoice(props.item)" > Paid  &emsp; <v-icon dark >send</v-icon></v-btn>
            <v-flex xs0 sm10 md10 v-else >
              <v-progress-linear :indeterminate="true" ></v-progress-linear>
            </v-flex>
        </div>
        <div v-else>
          <div v-if="dashboard == true ">
            <v-btn round color="primary" style="float: left;" dark primary hide-details @click="marckAsNotPaid(props.item)" > Not Paid  &emsp; <v-icon dark >edit</v-icon></v-btn>
          </div>
          <div v-else>
            <v-btn round color="primary" style="float: left;" dark primary hide-details @click="downloadInvoice(props.item)" > Download  &emsp; <v-icon dark >edit</v-icon></v-btn>
          </div>
        </div>
      </template>
      <template slot="expand"  slot-scope="props" >
        <v-card flat >
          <v-form ref="form" v-model="valid" lazy-validation>
                <v-layout style="margin: 0px;" justify-center >
                  <v-flex xs12 sm3 md3>
                    <v-text-field v-model="props.item.nif" label="Nif"  required  ></v-text-field> <!-- :rules="emailRules" -->
                  </v-flex>
                  <v-flex xs12 sm3 md3>
                    <v-text-field v-model="props.item.name" label="Nome"  required ></v-text-field> <!-- :rules="emailRules" -->
                  </v-flex>
                  <button class="btn btn-primary pl-5 pr-5" @click="download">Download PDF</button>
                  <v-flex xs12 sm3 md3 s>
                    <v-btn round color="green" style="margin-top: 20px;" dark hide-details @click="confirmInvoice(props.item); props.expanded = !props.expanded">  Confirmar  &emsp; <v-icon dark>done</v-icon></v-btn>
                    <v-btn round color="red" style="margin-top: 20px;" dark hide-details @click="props.expanded = !props.expanded">  Cancelar  &emsp; <v-icon dark>close</v-icon></v-btn>
                  </v-flex>
                </v-layout>
          </v-form>
        </v-card>
    </template>
    </v-data-table>
  </div>
</template>


<script>

  export default {
    props: ['dashboard', 'typeInvoice', 'alterInvoice'],
    data () {
      return{
        invoice: [],
        totalInvoices: 0,
        nif: [],
        nome: [],
        valid: "",
        updatedInvoice: false,
        pagination: { //added the first 2
            page: 1,
            rowsPerPage: 7,
            rowsPerPageItems: [7, 15, 30]
        },
        headers: [
            {
                text: 'Numero da Mesa',
                align: 'rigth',
                value: 'table_number',
                sortable: false,
            },
            { text: 'Estado', value: 'state', sortable: false },
            { text: 'Empregado responsavel', value: 'responsible_waiter_id', sortable: false },
            { text: 'Preço total', value: 'total_price_preview', sortable: false },
            {  sortable: false },
            {  sortable: false },
        ],
      }
    },
   watch: {
    pagination: {
      handler () {
        this.getPage().then(data => {
          this.invoice = data.items
          this.totalInvoices = data.total
        })
      },
      deep: true
      },
      alterInvoice: function(newVal, oldVal){
          let index = this.invoice.indexOf(newVal);
          this.invoice[index]  = newVal;
      },
    },
    methods:{
      getPage(page){ 
        console.log(this.typeInvoice);
        if(this.typeInvoice != 'downloadInvoice'){
            return new Promise((resolve, reject) => {
                  axios.get('api/invoice?page=' + (this.pagination.page ) + '&page_size=' + this.pagination.rowsPerPage).then(response=>{  
                  //   axios.get('api/invoice/pending').then(response=>{ 
                        let items = response.data.data;
                        let total = response.data.meta.total;
                        resolve({
                          items,
                          total
                        })
                    })
            })
        }else{
          return new Promise((resolve, reject) => {
            axios.get('api/invoice/paid?page=' + (this.pagination.page ) + '&page_size=' + this.pagination.rowsPerPage).then(response=>{  
            //   axios.get('api/invoice/pending').then(response=>{ 
                  let items = response.data.data;
                  let total = response.data.meta.total;
                  resolve({
                    items,
                    total
                  })
              })
          })
        }
      },
      confirmInvoice(invoiceAtual){
        const index = this.invoice.indexOf(invoiceAtual);
        axios.put('api/invoice/update/' + this.invoice[index].id, this.invoice[index]).then(response => {  
          this.updatedInvoice = true;   
          this.$socket.emit('invoice_state_cashier', 'Paid', 'list-orders',e);
        })
        .catch(function(err) {
          console.log(err);
        });
      },
      sendInvoice(invoiceAtual){
        
          const index = this.invoice.indexOf(invoiceAtual);
          axios.put('api/invoice/update/' + this.invoice[index].id, this.invoice[index]).then(response => {  
            this.invoice.splice(index, 1);
            let meal = response.data.data.meals;
            meal.state = 'paid';
            this.$socket.emit('invoice_sent', response.data.data, "Sent");     

            axios.put('api/meal/update/' + meal.id, meal).then(response => {  
            });
          })
          .catch(function(err) {
            console.log(err);

          });
      },
      marckAsNotPaid(invoiceAtual){
          invoiceAtual.state = 'not paid';

          //update invoice 
          const index = this.invoice.indexOf(invoiceAtual);
          axios.put('api/invoice/update/' + invoiceAtual.id, invoiceAtual).then(response => {  
            this.invoice.splice(index, 1);
            let meal = response.data.data.meals;
            this.$socket.emit('invoice_state_cashier', 'Not Paid', 'list-orders', e);

            //update respective meal
            meal.state = 'not paid';
            axios.put('api/meal/update/' + meal.id, meal).then(response => { 
                  let orders = response.data.data.orders 
                    orders.forEach(function(entry) {

                        // Update Meals
                        entry.state = "not delivered";
                        axios.put('api/order/update/state/notDeliverd/' + entry.id, entry).then(response => {  
                            orders[entry] = response.data.data
                        });
                    });

                  this.$emit('notPaidMeal', response.data.data);
            });

            //Soket
           
          })
          .catch(function(err) {
            console.log(err);

          });
      },
      downloadOrder(order){
            axios.get('api/pdf/' + invoice.id).then(response => {  
                const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', "invoice.pdf");
                        document.body.appendChild(link);
                        link.click();
            });
      },

      //Check vall
      emptyCheker(item){ 
        return (item.nif === "" || item.name === "" );
      },
      pendingCheker(item){
        return item.state === "pending";
      },

      //Soket
      changeStyleTemp: function(changedInvoice, index, type, time_ms){
            this.invoice[index].nif = changedInvoice.nif;
            this.invoice[index].name = changedInvoice.name;
        setTimeout( () => {
          this.changeType = "";
          this.tempStyleOrder = null;
        }, time_ms)
      },
    },
    sockets: {
        invoice_state_cashier(dataFromServer){
            this.getPage();
        },  	/*
        invoice_changed(changedInvoice){
          let index = this.invoice.findIndex(x => x.id === changedInvoice[0].id);
            if (index !== null) {
              this.changeStyleTemp(changedInvoice[0] ,index, changedInvoice[1], 3000);
            }   
        },  	
        invoice_sent(sentInvoice){
            let index = this.invoice.findIndex(x => x.id === sentInvoice[0].id);
            this.invoice.splice(index , 1);     
        },   */
    },
}
</script>