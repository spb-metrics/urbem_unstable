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
     * Classe de mapeamento para a tabela IMOBILIARIO.tipo_baixa
     * Data de Criação: 18/03/2008

     * @author Analista: Fabio Bertoldi
     * @author Desenvolvedor: Fernando Piccini Cercato

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMTipoBaixa.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCIMTipoBaixa extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCIMTipoBaixa()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.tipo_baixa');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_licenca,exercicio');

        $this->AddCampo( 'cod_tipo', 'integer', true, '', true, false );
        $this->AddCampo( 'nom_tipo', 'varchar', true, '100', false, false );
    }
}
