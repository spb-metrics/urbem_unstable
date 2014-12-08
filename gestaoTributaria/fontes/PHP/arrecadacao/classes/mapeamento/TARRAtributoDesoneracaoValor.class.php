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
  * Classe de mapeamento da tabela ARRECADACAO.ATRIBUTO_DESONERACAO_VALOR
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRAtributoDesoneracaoValor.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.04
*/

/*
$Log$
Revision 1.7  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.6  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRAtributoDesoneracao.class.php" );

/**
  * Efetua conexão com a tabela  ARRECADACAO.ATRIBUTO_DESONERACAO_VALOR
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRAtributoDesoneracaoValor extends PersistenteAtributosValores
{
/**
    * Método Construtor
    * @access Private
*/
function TARRAtributoDesoneracaoValor()
{
    parent::PersistenteAtributosValores();
    $this->setTabela('arrecadacao.atributo_desoneracao_valor');
    $this->setPersistenteAtributo( new TARRAtributoDesoneracao );

    $this->setCampoCod('');
    $this->setComplementoChave('cod_desoneracao,numcgm,cod_atributo,cod_cadastro');

    $this->AddCampo('cod_desoneracao','integer',true,'',true,true);
    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('cod_atributo','integer',true,'',true,true);
    $this->AddCampo('cod_cadastro','integer',true,'',true,true);
    $this->AddCampo('cod_modulo','integer',true,'',true,true);
    $this->AddCampo('ocorrencia','integer',true,'',false,false);
    $this->AddCampo('valor','text',true,'',false,false);

}
}
?>
