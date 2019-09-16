using AutoMapper;
using CustomerKeys.BLL.DTO;
using CustomerKeys.BLL.Interfaces;
using CustomerKeys.BLL.Utils;
using CustomerKeys.DAL.Entities;
using CustomerKeys.DAL.Interfaces;
using System;
using System.Collections.Generic;
using System.Linq;

namespace CustomerKeys.BLL.Services
{
    public class KeyService : IKeyService
    {
        IUnitOfWork Database { get; set; }

        public KeyService(IUnitOfWork uow)
        {
            Database = uow;
        }

        public IEnumerable<CustomerDTO> GetCustomers()
        {
            var mapper = new MapperConfiguration(cfg => cfg.CreateMap<Customer, CustomerDTO>()).CreateMapper();
            return mapper.Map<IEnumerable<Customer>, List<CustomerDTO>>(Database.Customers.GetAll());
        }

        public IEnumerable<KeyDTO> GetKeys(int customerId)
        {
            var mapper = new MapperConfiguration(cfg => cfg.CreateMap<Key, KeyDTO>()).CreateMapper();
            return mapper.Map<IEnumerable<Key>, List<KeyDTO>>(Database.Keys.Find(delegate (Key k) { return k.CustomerId == customerId; }));
        }

        public bool GenerateKey(CustomerDTO customerDTO, int keyNumbers)
        {
            try
            {
                Customer customer = Database.Customers.Get(customerDTO.Id);

                if (customer == null)
                {
                    customer = Database.Customers.Create(new Customer() { Name = customerDTO.Name });
                    Database.Save();
                }

                for (int i = 0; i < keyNumbers; i++)
                {
                    // Generates a key
                    string key = Helper.GetKey();
                    bool isFound = true;

                    while (isFound)
                    {
                        // Checks if exist key in database
                        isFound = IsExistsKey(key);

                        // If exists the key then generates a new unique key
                        if (isFound)
                        {
                            key = Helper.GetKey();
                        }
                    }

                    // Adds the key to database
                    Database.Keys.Create(new Key { CustomerId = customer.Id, Value = key });
                }

                Database.Save();
            }
            catch
            {
                return false;
            }

            return true;
        }

        public bool IsExistsKey(string key)
        {
            Func<Key, bool> FindKey = delegate (Key k) { return k.Value.Equals(key, StringComparison.OrdinalIgnoreCase); };

            return Database.Keys.Find(FindKey).ToList().Count > 0;
        }
        
        public void Dispose()
        {
            Database.Dispose();
        }

    }
}
