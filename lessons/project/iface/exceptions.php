<?php

declare(strict_types=1);

namespace App\Iface;

// class FileReadException extends \Exception implements IFileException {}

// class FileWriteException extends \Exception implements IFileException {}

// class FileReadException extends Exception implements IFileException {}
// class FileWriteException extends Exception implements IFileException {}
// class FileNotFoundException extends Exception implements IFileException {}

class ValidationException extends \Exception implements IUserException {}
// class CategoryNotFoundException extends Exception implements IUserException {}
// class ProductNotFoundException extends Exception implements IUserException {}

// class NetworkException extends Exception implements INetException {}
