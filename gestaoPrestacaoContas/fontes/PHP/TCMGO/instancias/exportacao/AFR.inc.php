<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Data de Criação   : 02/03/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 61525 $
    $Name$
    $Author: evandro $
    $Date: 2015-01-29 17:21:26 -0200 (Qui, 29 Jan 2015) $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.6  2007/06/12 18:33:46  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.5  2007/06/05 19:16:48  bruce
corrigido o sequencial

Revision 1.4  2007/05/24 20:51:49  bruce
corrigido o retorno da pl e feita ligação com a Unidade

Revision 1.3  2007/05/15 20:46:24  bruce
acrescentado o tipo de lançamento

Revision 1.2  2007/05/15 13:40:48  bruce
buscando o orgão

Revision 1.1  2007/05/07 19:50:43  bruce
*** empty log message ***

Revision 1.5  2007/04/26 20:27:53  bruce
*** empty log message ***

Revision 1.4  2007/04/25 20:33:26  bruce
correções no formato dos campos monetários

Revision 1.3  2007/04/24 15:34:16  bruce
correções

Revision 1.2  2007/04/24 13:47:24  bruce
corrigida ultima linha do arquivo

Revision 1.1  2007/04/20 20:24:27  bruce
Bug #9169#

*/
include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOAtivoFinanceiro.class.php" );

$obTMapeamento = new TTCMGOAtivoFinanceiro;
$obTMapeamento->setDado ('exercicio'  , Sessao::getExercicio() );
$obTMapeamento->setDado ('stEntidades', $stEntidades  );
$obTMapeamento->recuperaArquivoExportacao10($rsRegistro10,"","",$boTransacao);
$obTMapeamento->recuperaArquivoExportacao11($rsRegistro11,"","",$boTransacao);

$i = 0;        
$j = 0;

if ($rsRegistro10->getNumLinhas() > 0) {
    $stChave10 = '';
    $stChaveAuxiliar10 = '';    
    foreach ($rsRegistro10->arElementos as $stChave) {
        $rsRegistro10->arElementos[$i]['numero_registro']    = $i+1;
        $rsRegistro10->arElementos[$i]['vl_cancelamento']    = 0;
        $rsRegistro10->arElementos[$i]['vl_encampacao']      = 0;
        $i++;
    }   
    foreach ($rsRegistro10->getElementos() as $arRegistro10) {
        $stChaveAuxiliar10 = $arRegistro10['num_orgao'] . $arRegistro10['num_unidade'] . $arRegistro10['exercicio'] . $arRegistro10['tipo_lancamento'];
        if ( $stChaveAuxiliar10 != $stChave10 ) {
            $stChave10 = $arRegistro10['num_orgao'] . $arRegistro10['num_unidade'] . $arRegistro10['exercicio'] . $arRegistro10['tipo_lancamento'];

            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arRegistro10));
        
            $obExportador->roUltimoArquivo->addBloco($$rsBloco);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_conta");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(200);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_lancamento" );
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 3 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_anterior");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_creditos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_debitos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cancelamento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_encampacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_atual");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        
            if ($rsRegistro11->getNumLinhas() > 0) {
                $stChave11 = '';                                
                foreach ($rsRegistro11->arElementos as $stChave) {                    
                    $rsRegistro11->arElementos[$j]['numero_registro']    = $j+1;
                    $rsRegistro11->arElementos[$j]['vl_cancelamento']    = 0;
                    $rsRegistro11->arElementos[$j]['vl_encampacao']      = 0;
                    $j++;
                }

                foreach ($rsRegistro11->getElementos() as $arRegistro11) {
                    $stChave20Aux = $arRegistro11['num_orgao'] . $arRegistro11['num_unidade'] . $arRegistro11['exercicio'] . $arRegistro11['tipo_lancamento'];
                    //Verifica se registro 20 bate com chave do registro 10
                    if ($stChave10 === $stChave20Aux) {
                        //Chave única do registro 20
                        if ($stChave20 != $stChave20Aux ) {
                            $stChave20 = $stChave20Aux;

                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arRegistro11));
        
                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );
    
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_conta");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(200);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_lancamento" );
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 3 );

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte" );
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_anterior");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_creditos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_debitos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cancelamento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_encampacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_atual");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                        }
                    }
                }
            }
        }
    }
}

$rsRecordSetRodape = new RecordSet;

$arRegistro = array();
$arRegistro[0][ 'tipo_registro'  ] = 99 ;
$arRegistro[0][ 'brancos'        ] = ' ';
$arRegistro[0][ 'numero_registro'] = count($rsRegistro10->getElementos()) + count($rsRegistro11->getElementos());

$rsRecordSetRodape->preenche ( $arRegistro );

$obExportador->roUltimoArquivo->addBloco( $rsRecordSetRodape );
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 295 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

unset($rsRegistro10);
unset($rsRegistro11);

?>