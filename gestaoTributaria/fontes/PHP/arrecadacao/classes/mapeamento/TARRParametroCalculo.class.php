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
    * Classe de mapeamento da tabela ARRECADACAO.PARAMETRO_CALCULO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRParametroCalculo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.7  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.6  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.PARAMETRO_CALCULO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRParametroCalculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRParametroCalculo()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.parametro_calculo');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_credito,cod_natureza,cod_genero,cod_especie,ocorrencia_credito');

    $this->AddCampo('cod_credito','integer',true,'',true,true);
    $this->AddCampo('cod_natureza','integer',true,'',true,true);
    $this->AddCampo('cod_genero','integer',true,'',true,true);
    $this->AddCampo('cod_especie','integer',true,'',true,true);
    $this->AddCampo('ocorrencia_credito','integer',true,'',true,false);
    $this->AddCampo('cod_funcao','integer',true,'',false,true);
    $this->AddCampo('cod_biblioteca','integer',true,'',false,true);
    $this->AddCampo('cod_modulo','integer',true,'',false,true);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('valor_correspondente','varchar',false,'20',false,false);

}
}
?>
