<?php

namespace Wikibase\Client\Tests;

use PHPUnit\Framework\TestCase;
use Wikibase\Client\EntitySourceDefinitionsLegacyClientSettingsParser;
use Wikibase\Lib\EntityTypeDefinitions;
use Wikibase\SettingsArray;

/**
 * @covers \Wikibase\Client\EntitySourceDefinitionsLegacyClientSettingsParser
 *
 * @group Wikibase
 * @group WikibaseClient
 *
 * @license GPL-2.0-or-later
 */
class EntitySourceDefinitionsLegacyClientSettingsParserTest extends TestCase {

	public function testGivenDedicatedSettingsDefined_localSourceDefinedByThese() {
		$settings = [
			'repoDatabase' => 'testdb',
			'entityNamespaces' => [ 'item' => 100 ],
			'repoConceptBaseUri' => 'test://concept/',
			'repositories' => [],
		];

		$parser = new EntitySourceDefinitionsLegacyClientSettingsParser();

		$sourceDefinitions = $parser->newDefinitionsFromSettings(
			new SettingsArray( $settings ),
			new EntityTypeDefinitions( [] )
		);

		$sources = $sourceDefinitions->getSources();

		$this->assertCount( 1, $sources );

		$this->assertSame( 'local', $sources[0]->getSourceName() );
		$this->assertSame( 'testdb', $sources[0]->getDatabaseName() );
		$this->assertEquals( [ 'item' ], $sources[0]->getEntityTypes() );
		$this->assertEquals( [ 'item' => 100 ], $sources[0]->getEntityNamespaceIds() );
		$this->assertEquals( [ 'item' => 'main' ], $sources[0]->getEntitySlotNames() );
		$this->assertEquals( 'test://concept/', $sources[0]->getConceptBaseUri() );
	}

	public function testGivenRepositoriesSettingsDefined_localSourceDefinedByThese() {
		$settings = [
			'repositories' => [
				'' => [
					'repoDatabase' => 'testdb',
					'entityNamespaces' => [ 'item' => 100 ],
					'baseUri' => 'test://concept/',
				],
				'foo' => [
					'repoDatabase' => 'foodb',
					'entityNamespaces' => [ 'property' => 200 ],
					'baseUri' => 'foo://concept/',
				],
			],
		];

		$parser = new EntitySourceDefinitionsLegacyClientSettingsParser();

		$sourceDefinitions = $parser->newDefinitionsFromSettings(
			new SettingsArray( $settings ),
			new EntityTypeDefinitions( [] )
		);

		$sources = $sourceDefinitions->getSources();

		$this->assertCount( 2, $sources );

		$this->assertSame( 'local', $sources[0]->getSourceName() );
		$this->assertSame( 'testdb', $sources[0]->getDatabaseName() );
		$this->assertEquals( [ 'item' ], $sources[0]->getEntityTypes() );
		$this->assertEquals( [ 'item' => 100 ], $sources[0]->getEntityNamespaceIds() );
		$this->assertEquals( [ 'item' => 'main' ], $sources[0]->getEntitySlotNames() );
		$this->assertEquals( 'test://concept/', $sources[0]->getConceptBaseUri() );

		$this->assertSame( 'foo', $sources[1]->getSourceName() );
		$this->assertSame( 'foodb', $sources[1]->getDatabaseName() );
		$this->assertEquals( [ 'property' ], $sources[1]->getEntityTypes() );
		$this->assertEquals( [ 'property' => 200 ], $sources[1]->getEntityNamespaceIds() );
		$this->assertEquals( [ 'property' => 'main' ], $sources[1]->getEntitySlotNames() );
		$this->assertEquals( 'foo://concept/', $sources[1]->getConceptBaseUri() );
	}

	public function testGivenForeignRepositoriesSettingsDefined_localSourceDefinedByThese() {
		$settings = [
			'foreignRepositories' => [
				'' => [
					'repoDatabase' => 'testdb',
					'entityNamespaces' => [ 'item' => 100 ],
					'baseUri' => 'test://concept/',
				],
				'foo' => [
					'repoDatabase' => 'foodb',
					'entityNamespaces' => [ 'property' => 200 ],
					'baseUri' => 'foo://concept/',
				],
			],
		];

		$parser = new EntitySourceDefinitionsLegacyClientSettingsParser();

		$sourceDefinitions = $parser->newDefinitionsFromSettings(
			new SettingsArray( $settings ),
			new EntityTypeDefinitions( [] )
		);

		$sources = $sourceDefinitions->getSources();

		$this->assertCount( 2, $sources );

		$this->assertSame( 'local', $sources[0]->getSourceName() );
		$this->assertSame( 'testdb', $sources[0]->getDatabaseName() );
		$this->assertEquals( [ 'item' ], $sources[0]->getEntityTypes() );
		$this->assertEquals( [ 'item' => 100 ], $sources[0]->getEntityNamespaceIds() );
		$this->assertEquals( [ 'item' => 'main' ], $sources[0]->getEntitySlotNames() );
		$this->assertEquals( 'test://concept/', $sources[0]->getConceptBaseUri() );

		$this->assertSame( 'foo', $sources[1]->getSourceName() );
		$this->assertSame( 'foodb', $sources[1]->getDatabaseName() );
		$this->assertEquals( [ 'property' ], $sources[1]->getEntityTypes() );
		$this->assertEquals( [ 'property' => 200 ], $sources[1]->getEntityNamespaceIds() );
		$this->assertEquals( [ 'property' => 'main' ], $sources[1]->getEntitySlotNames() );
		$this->assertEquals( 'foo://concept/', $sources[1]->getConceptBaseUri() );
	}

	public function testGivenForeignRepositoriesAndRepositoriesSettingsDefined_foreignRepositoriesDefinitionPreferred() {
		$settings = [
			'foreignRepositories' => [
				'' => [
					'repoDatabase' => 'testdb',
					'entityNamespaces' => [ 'item' => 100 ],
					'baseUri' => 'test://concept/',
				],
			],
			'repositories' => [
				'' => [
					'repoDatabase' => 'testdb2',
					'entityNamespaces' => [ 'item' => 200 ],
					'baseUri' => 'test2://concept/',
				],
			],
		];

		$parser = new EntitySourceDefinitionsLegacyClientSettingsParser();

		$sourceDefinitions = $parser->newDefinitionsFromSettings(
			new SettingsArray( $settings ),
			new EntityTypeDefinitions( [] )
		);

		$sources = $sourceDefinitions->getSources();

		$this->assertCount( 1, $sources );

		$this->assertSame( 'local', $sources[0]->getSourceName() );
		$this->assertSame( 'testdb', $sources[0]->getDatabaseName() );
		$this->assertEquals( [ 'item' ], $sources[0]->getEntityTypes() );
		$this->assertEquals( [ 'item' => 100 ], $sources[0]->getEntityNamespaceIds() );
		$this->assertEquals( [ 'item' => 'main' ], $sources[0]->getEntitySlotNames() );
		$this->assertEquals( 'test://concept/', $sources[0]->getConceptBaseUri() );
	}

	public function testGivenRepositoriesAndDedicatedSettingsDefined_dedicatedSettingsPreferredForLocalRepository() {
		$settings = [
			'repoDatabase' => 'testdb',
			'entityNamespaces' => [ 'item' => 100 ],
			'repoConceptBaseUri' => 'test://concept/',
			'repositories' => [
				'' => [
					'repoDatabase' => 'testdb2',
					'entityNamespaces' => [ 'item' => 200 ],
					'baseUri' => 'test2://concept/',
				],
			],
		];

		$parser = new EntitySourceDefinitionsLegacyClientSettingsParser();

		$sourceDefinitions = $parser->newDefinitionsFromSettings(
			new SettingsArray( $settings ),
			new EntityTypeDefinitions( [] )
		);

		$sources = $sourceDefinitions->getSources();

		$this->assertCount( 1, $sources );

		$this->assertSame( 'local', $sources[0]->getSourceName() );
		$this->assertSame( 'testdb', $sources[0]->getDatabaseName() );
		$this->assertEquals( [ 'item' ], $sources[0]->getEntityTypes() );
		$this->assertEquals( [ 'item' => 100 ], $sources[0]->getEntityNamespaceIds() );
		$this->assertEquals( [ 'item' => 'main' ], $sources[0]->getEntitySlotNames() );
		$this->assertEquals( 'test://concept/', $sources[0]->getConceptBaseUri() );
	}

	public function testRepositoryPrefixIsUsedAsInterwikiPrefix() {
		$settings = [
			'repositories' => [
				'' => [
					'repoDatabase' => 'testdb',
					'entityNamespaces' => [ 'item' => 100 ],
					'baseUri' => 'test://concept/',
				],
				'foo' => [
					'repoDatabase' => 'foodb',
					'entityNamespaces' => [ 'property' => 200 ],
					'baseUri' => 'foo://concept/',
				],
			],
		];

		$parser = new EntitySourceDefinitionsLegacyClientSettingsParser();

		$sourceDefinitions = $parser->newDefinitionsFromSettings(
			new SettingsArray( $settings ),
			new EntityTypeDefinitions( [] )
		);

		$sources = $sourceDefinitions->getSources();

		$this->assertCount( 2, $sources );

		$this->assertSame( 'local', $sources[0]->getSourceName() );
		$this->assertSame( '', $sources[0]->getInterwikiPrefix() );

		$this->assertSame( 'foo', $sources[1]->getSourceName() );
		$this->assertSame( 'foo', $sources[1]->getInterwikiPrefix() );
	}

	public function testWikidataDefaultsUsedForRdfNamespacePrefixesForTheLocalSource() {
		$settings = [
			'repositories' => [
				'' => [
					'repoDatabase' => 'testdb',
					'entityNamespaces' => [ 'item' => 100 ],
					'baseUri' => 'test://concept/',
				],
			],
		];

		$parser = new EntitySourceDefinitionsLegacyClientSettingsParser();

		$sourceDefinitions = $parser->newDefinitionsFromSettings(
			new SettingsArray( $settings ),
			new EntityTypeDefinitions( [] )
		);

		$sources = $sourceDefinitions->getSources();

		$this->assertCount( 1, $sources );

		$this->assertSame( 'local', $sources[0]->getSourceName() );
		$this->assertSame( 'wd', $sources[0]->getRdfNodeNamespacePrefix() );
		$this->assertSame( '', $sources[0]->getRdfPredicateNamespacePrefix() );
	}

	public function testRepositoryPrefixIsUsedForRdfNamespacePrefixesForNonLocalSource() {
		$settings = [
			'repositories' => [
				'' => [
					'repoDatabase' => 'testdb',
					'entityNamespaces' => [ 'item' => 100 ],
					'baseUri' => 'test://concept/',
				],
				'foo' => [
					'repoDatabase' => 'foodb',
					'entityNamespaces' => [ 'property' => 200 ],
					'baseUri' => 'foo://concept/',
				],
			],
		];

		$parser = new EntitySourceDefinitionsLegacyClientSettingsParser();

		$sourceDefinitions = $parser->newDefinitionsFromSettings(
			new SettingsArray( $settings ),
			new EntityTypeDefinitions( [] )
		);

		$sources = $sourceDefinitions->getSources();

		$this->assertCount( 2, $sources );

		$this->assertSame( 'foo', $sources[1]->getSourceName() );
		$this->assertSame( 'foo', $sources[1]->getRdfNodeNamespacePrefix() );
		$this->assertSame( 'foo', $sources[1]->getRdfPredicateNamespacePrefix() );
	}

	public function testSlotDefinitionsAlongWithNamespaceIdAreHandled() {
		$settings = [
			'repositories' => [
				'' => [
					'repoDatabase' => 'testdb',
					'entityNamespaces' => [ 'item' => '100/itemz' ],
					'baseUri' => 'test://concept/',
				],
			],
		];

		$parser = new EntitySourceDefinitionsLegacyClientSettingsParser();

		$sourceDefinitions = $parser->newDefinitionsFromSettings(
			new SettingsArray( $settings ),
			new EntityTypeDefinitions( [] )
		);

		$sources = $sourceDefinitions->getSources();

		$this->assertCount( 1, $sources );

		$this->assertSame( 'local', $sources[0]->getSourceName() );
		$this->assertEquals( [ 'item' ], $sources[0]->getEntityTypes() );
		$this->assertEquals( [ 'item' => 100 ], $sources[0]->getEntityNamespaceIds() );
		$this->assertEquals( [ 'item' => 'itemz' ], $sources[0]->getEntitySlotNames() );
	}

}
