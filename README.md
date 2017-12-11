platform
========

A Symfony project created on April 17, 2017, 6:05 am.


### Directory structure:
- `_docs` =>  contains all pictures and additional documentation for documentation
- `app/config` => configuration part of the website
    - [yaml][sf_yaml] is simple
    - `config` => global configuration
    - `config_%s` => configuration for certain environments
    - `parameters` => sensitive informations (future SF releases: env() usage)
        - [Configuration (Best Practices)][sf_config_parameters_1]
        - [Introduction to Parameters (current)][sf_config_parameters_2]
        - [How to Set external Parameters in the Service Container][sf_config_parameters_3]
    - `routing` => routing configuration (obsolete; using Annotations)
        - [@Route and @Method][sf_routing_annotations]
    - `security` => security parameters, such as password encoders, firewall
        - [Security][sf_security]
        - [How to manually encode a password][sf_security_password_encode]
        - [How to restrict firewalls to a specific route][sf_security_firewall]
        - [How to load security users from the database][sf_security_entity_provider]
    - `services` => for defining services
        - [Service Container][sf_service_container]
        - [How to define controllers as services][sf_service_controller]




[sf_yaml]: https://symfony.com/doc/current/components/yaml/yaml_format.html
[sf_config_parameters_1]: https://symfony.com/doc/current/best_practices/configuration.html#infrastructure-related-configuration
[sf_config_parameters_2]: https://symfony.com/doc/current/service_container/parameters.html
[sf_config_parameters_3]: https://symfony.com/doc/current/configuration/external_parameters.html
[sf_routing_annotations]: https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/routing.html
[sf_security]: https://symfony.com/doc/current/security.html
[sf_security_password_encode]: https://symfony.com/doc/current/security/password_encoding.html
[sf_security_firewall]: https://symfony.com/doc/current/security/firewall_restriction.html
[sf_security_entity_provider]: https://symfony.com/doc/current/security/entity_provider.html
[sf_service_container]: https://symfony.com/doc/current/service_container.html
[sf_service_controller]: https://symfony.com/doc/current/controller/service.html