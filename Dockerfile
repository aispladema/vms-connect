FROM php:7.0-apache

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data