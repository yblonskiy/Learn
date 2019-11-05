using DayBook.Application.Interfaces;
using Microsoft.Extensions.Hosting;
using Microsoft.Extensions.Logging;
using System;
using System.Threading;
using System.Threading.Tasks;
using Microsoft.Extensions.DependencyInjection;

namespace DayBook.Web
{
    internal interface IScopedProcessingService
    {
        Task DoWork(CancellationToken stoppingToken);
    }

    internal class ScopedProcessingService : IScopedProcessingService
    {
        private readonly IManageService _manageService;

        public ScopedProcessingService(IManageService manageService)
        {
            _manageService = manageService;
        }

        public async Task DoWork(CancellationToken stoppingToken)
        {
            while (!stoppingToken.IsCancellationRequested)
            {
                await _manageService.ClearUnusedAsync();

                await Task.Delay(TimeSpan.FromMinutes(30), stoppingToken);
            }
        }
    }

    public class ConsumeScopedManageHostedService : BackgroundService
    {
        private readonly ILogger<ConsumeScopedManageHostedService> _logger;

        public ConsumeScopedManageHostedService(IServiceProvider services,
            ILogger<ConsumeScopedManageHostedService> logger)
        {
            Services = services;
            _logger = logger;
        }

        public IServiceProvider Services { get; }

        protected override async Task ExecuteAsync(CancellationToken stoppingToken)
        {
            //_logger.LogInformation(
            //    "Consume Scoped Service Hosted Service running.");

            await DoWork(stoppingToken);
        }

        private async Task DoWork(CancellationToken stoppingToken)
        {
            //_logger.LogInformation(
            //    "Consume Scoped Service Hosted Service is working.");

            using (var scope = Services.CreateScope())
            {
                var scopedProcessingService =
                    scope.ServiceProvider
                        .GetRequiredService<IScopedProcessingService>();

                await scopedProcessingService.DoWork(stoppingToken);
            }
        }

        public override async Task StopAsync(CancellationToken stoppingToken)
        {           
            await Task.CompletedTask;
        }
    }
}
