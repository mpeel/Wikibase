import Dispatcher from '@/mediawiki/Dispatcher';
import MwWindow from '@/@types/mediawiki/MwWindow';
import AppBridge from '@/definitions/AppBridge';
import EditFlow from '@/definitions/EditFlow';

const manager = jest.fn();
const dialog = {
	getManager: jest.fn( () => manager ),
};
const mockPrepareContainer = jest.fn( ( _x?: any, _y?: any, _z?: any ) => {
	return dialog;
} );
jest.mock( '@/mediawiki/prepareContainer', () => ( {
	__esModule: true,
	default: ( oo: any, $: any, id: any ) => mockPrepareContainer( oo, $, id ),
} ) );

const mockCreateServices = jest.fn();
jest.mock( '@/mediawiki/createServices', () => ( {
	__esModule: true,
	default: ( mwWindow: any ) => mockCreateServices( mwWindow ),
} ) );

const mockSubscribeToAppEvents = jest.fn();
jest.mock( '@/mediawiki/subscribeToAppEvents', () => ( {
	__esModule: true,
	default: ( emitter: any, windowManager: any ) => mockSubscribeToAppEvents( emitter, windowManager ),
} ) );

describe( 'Dispatcher', () => {
	it( 'can be constructed with mwWindow and app definition', () => {
		const dispatcher = new Dispatcher(
			{} as MwWindow,
			{} as AppBridge,
		);
		expect( dispatcher ).toBeInstanceOf( Dispatcher );
	} );

	describe( 'dispatch', () => {
		it( 'prepares the DOM container', () => {
			const OO = new ( jest.fn() )();
			const $ = new ( jest.fn() )();
			const dispatcher = new Dispatcher(
				{
					OO,
					$,
				} as MwWindow,
				{
					launch: jest.fn(),
				},
			);

			dispatcher.dispatch( new ( jest.fn() )() );

			expect( mockPrepareContainer ).toHaveBeenCalledTimes( 1 );
			expect( mockPrepareContainer.mock.calls[ 0 ][ 0 ] ).toBe( OO );
			expect( mockPrepareContainer.mock.calls[ 0 ][ 1 ] ).toBe( $ );
			expect( mockPrepareContainer.mock.calls[ 0 ][ 2 ] ).toBe( Dispatcher.APP_DOM_CONTAINER_ID );
		} );

		it( 'triggers service creation and launches app', () => {
			const mwWindow = new ( jest.fn() )();
			const emitter = jest.fn();
			const app = {
				launch: jest.fn( () => {
					return emitter;
				} ),
			};
			const entityId = 'Q4711';
			const propertyId = 'P815';
			const editFlow = EditFlow.OVERWRITE;
			const mockServices = {};

			mockCreateServices.mockImplementation( () => mockServices );

			const dispatcher = new Dispatcher(
				mwWindow,
				app as any,
			);

			dispatcher.dispatch( {
				link: new ( jest.fn() )(),
				entityId,
				propertyId,
				editFlow,
			} );

			expect( mockCreateServices ).toHaveBeenCalledWith( mwWindow );
			expect( app.launch ).toHaveBeenCalledWith(
				{
					containerSelector: `#${Dispatcher.APP_DOM_CONTAINER_ID}`,
				},
				{
					entityId,
					propertyId,
					editFlow,
				},
				mockServices,
			);

			expect( mockSubscribeToAppEvents ).toBeCalledWith( emitter, manager );
		} );
	} );
} );
